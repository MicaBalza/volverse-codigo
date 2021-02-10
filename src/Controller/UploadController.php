<?php
namespace App\Controller;

use App\Entity\Capsule;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadController extends AbstractController
{
    private $emailService;

    public function __construct(EmailService $emailService) {
        $this->emailService = $emailService;
    }

    /**
     * @Route("/subir", name="upload")
     */
    public function upload(Request $request, SluggerInterface $slugger) {
        if ($request->get('code')) {
            return $this->render('upload-complete.html.twig', [
                'code' => $request->get('code')
            ]);
        }

        $capsule = new Capsule();
        $form = $this->buildForm($capsule);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->redirectToRoute('upload', [
                    'error' => 'Revise la información ingresada y vuelva a intentarlo'
                ]);
            }

            $files = $form->get('file')->getData();
            if (!$files) {
                return $this->redirectToRoute('upload', [
                    'error' => 'Debe subir al menos un archivo'
                ]);
            }

            $filenames = $this->processUploadedFiles($files, $slugger);
            if (!$filenames) {
                return $this->redirectToRoute('upload', [
                    'error' => 'Se produjo un error durante la subida de archivos. Vuelva a intentarlo.'
                ]);
            }

            $capsule->setAttachedFiles($filenames);
            $capsule->setArchived(false);
            $code = $capsule->generateCode();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($capsule);
            $entityManager->flush();
            $this->emailService->sendCreatedEmail($capsule);

            return $this->redirectToRoute('upload', ['code' => $code]);
        }

        return $this->render('upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function buildForm(Capsule $capsule) {
        return $this->createFormBuilder($capsule)
            ->add('kind', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'audio',
                    'video',
                    'text',
                    'image'
                ]
            ])
            ->add('file', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('ownerEmail', EmailType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    private function processUploadedFiles($files, $slugger) {
        $filenames = [];
        foreach ($files as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                foreach ($filenames as $filename) {
                    unlink($this->getParameter('uploads_directory') . "/$filename");
                }

                return null;
            }

            $filenames[] = $newFilename;
        }

        return $filenames;
    }
}