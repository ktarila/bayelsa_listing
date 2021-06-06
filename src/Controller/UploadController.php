<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Entity\Upload;
use App\Repository\UploadRepository;
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

        return $this->json(['id' => $upload->getId(), 'token' => $upload->getUploadToken()]);
    }

    #[Route('/remove', name: 'upload_delete', methods: ['DELETE'])]
    public function delete(Request $request, UploadRepository $uploadRepository): Response
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $upload = $uploadRepository->findOneByFields($data);
            if (null !== $upload) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($upload);
                $entityManager->flush();
            }
        }

        return $this->json(['msg' => 'removed']);
    }
}
