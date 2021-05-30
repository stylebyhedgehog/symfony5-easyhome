<?php


namespace App\Service;


use App\Entity\Ad;
use App\Entity\AdImage;
use Doctrine\Common\Collections\ArrayCollection;
use PhpOffice\PhpWord\Style\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private $targetDirectory;
    private $slugger;
    private $appKernel;

    public function __construct(SluggerInterface $slugger, KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
        $this->slugger = $slugger;
    }

    public function enc(UploadedFile $image)
    {
        $originalFilename = $image->getClientOriginalName();
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
        return $fileName;
    }

    public function uploadImage(array $images, Ad $ad)
    {
        $upload_directory = "images";
        foreach ($images as $image) {
            $imageAd = new AdImage();
            $originalFilename = $image->getClientOriginalName();
            $image->move(
                $upload_directory,
                $originalFilename
            );
            $imageAd->setFilename($originalFilename);
            $ad->addImage($imageAd);
        }
        return $ad;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function removeImagesByAd(Ad $ad)
    {
        $imgDirectory = $this->appKernel->getProjectDir() . '/public/images/';
        $filesystem = new Filesystem();
        foreach ($ad->getImages() as $image) {
            $filesystem->remove($imgDirectory.$image->getFilename());
        }
    }
}