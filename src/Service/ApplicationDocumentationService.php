<?php


namespace App\Service;


use App\Entity\Application;

use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpKernel\KernelInterface;

class ApplicationDocumentationService
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public function generate(Application $application)
    {
        $publicDirectory = $this->appKernel->getProjectDir() . '/public';
        $docDirectory = $publicDirectory . "/" . "doc" . "/";
        $word = new TemplateProcessor($docDirectory . "/rent.docx");
        $word = $this->rent($application, $word);
        $word->saveAs($docDirectory . "document_" . $application->getId() . ".docx");
    }

    private function rent(Application $application, TemplateProcessor $word)
    {
        $word->setValue('owner_surname', $application->getOwner()->getPersonalData()->getSurname());
        $word->setValue('owner_name', $application->getOwner()->getPersonalData()->getName());
        $word->setValue('sender_surname', $application->getSender()->getPersonalData()->getSurname());
        $word->setValue('sender_name', $application->getSender()->getPersonalData()->getName());
        $word->setValue('city', $application->getAd()->getCity());
        $word->setValue('street', $application->getAd()->getStreet());
        $word->setValue('street_type', $application->getAd()->getStreetType());
        $word->setValue('house_number', $application->getAd()->getHouseNumber());
        $word->setValue('flat_number', $application->getAd()->getFlatNumber());
        $word->setValue('price', $application->getAd()->getPrice());
        $word->setValue('sqr', $application->getAd()->getSqr());
        return $word;
    }
}