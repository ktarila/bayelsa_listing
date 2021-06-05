<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Entity\Upload;
use App\Form\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/upload')]
class UploadController extends AbstractController
{
    #[Route('/ajax/upload/{upload_token}', name: 'dropzone_upload', methods: ['POST'])]
    public function index(Request $request, string $upload_token, ValidatorInterface $validator): Response
    {
        $file = $request->files->get('file');
        $upload = new Upload();
        $upload->setImageFile($file);
        $upload->setUploadToken($upload_token);

        $errors = $validator->validate($upload);
        if (0 === \count($errors)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($upload);
            $entityManager->flush();
        }

        return $this->json(['id' => $upload->getId()]);
    }

    #[Route('/new', name: 'upload_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($upload);
            $entityManager->flush();

            return $this->redirectToRoute('upload_index');
        }

        return $this->render('upload/new.html.twig', [
            'upload' => $upload,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'upload_show', methods: ['GET'])]
    public function show(Upload $upload): Response
    {
        return $this->render('upload/show.html.twig', [
            'upload' => $upload,
        ]);
    }

    #[Route('/{id}/edit', name: 'upload_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Upload $upload): Response
    {
        $form = $this->createForm(UploadType::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('upload_index');
        }

        return $this->render('upload/edit.html.twig', [
            'upload' => $upload,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'upload_delete', methods: ['POST'])]
    public function delete(Request $request, Upload $upload): Response
    {
        if ($this->isCsrfTokenValid('delete'.$upload->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($upload);
            $entityManager->flush();
        }

        return $this->redirectToRoute('upload_index');
    }
}
