<?php

namespace AcMarche\Sepulture\History;

use AcMarche\Sepulture\Repository\SepultureRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class HistoryUtils
{
    public function __construct(
        private SerializerInterface $serializer,
        private SepultureRepository $sepultureRepository,
        private Security $security,
        private HistoryRepository $historyRepository,
    ) {
    }

    public function diffFiche(Fiche $fiche): void
    {
        $username = $this->getUsername();

        $originalData = $this->ficheRepository->getOriginalEntityData($fiche);
        $toArrayEntity = $this->ficheToArray($fiche);
        unset($toArrayEntity['created_at']);
        unset($toArrayEntity['updated_at']);
        unset($toArrayEntity['id']);

        $changes = array_diff_assoc($toArrayEntity, $originalData);
        foreach ($changes as $property => $change) {
            $this->createForFiche($fiche, $username, $property, $originalData[$property], $change);
        }
        if ([] !== $changes) {
            $this->historyRepository->flush();
        }
    }

    private function ficheToArray(Fiche $fiche): array
    {
        $data = $this->serializer->serialize($fiche, 'json', ['groups' => 'group1']);

        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    private function getUsername(): string
    {
        $username = null;
        if (($user = $this->security->getUser()) !== null) {
            $username = $user->getUserIdentifier();
        }
        if ($username === null) {
            $username = "token";
        }

        return $username;
    }

    private function createForFiche(
        ?Fiche $fiche,
        ?string $made_by,
        ?string $property,
        ?string $oldValue,
        ?string $newValue
    ): void {
        $history = new History($fiche, $made_by, $property, $oldValue, $newValue);
        $this->historyRepository->persist($history);
    }

    public function diffClassement(Fiche $fiche, Category $category, string $action): void
    {
        $username = $this->getUsername();
        $path = $this->pathUtils->getPath($category);
        $classementPath = implode(' > ', $path);
        $this->createForFiche($fiche, $username, 'classement', $action, $classementPath);
        $this->historyRepository->flush();
    }

    public function newFiche(Fiche $fiche): void
    {
        $username = $this->getUsername();
        $this->createForFiche($fiche, $username, 'nouvelle fiche', '', '');
        $this->historyRepository->flush();
    }

    public function deleteFiche(string $nomFiche): void
    {
        $username = $this->getUsername();
        $this->createForFiche(null, $username, 'suppression de fiche', $nomFiche, '');
        $this->historyRepository->flush();
    }

    public function addImage(Fiche $fiche, FicheImage $ficheImage)
    {
        $username = $this->getUsername();
        $this->createForFiche($fiche, $username, 'ajout image', '', $ficheImage->getImageName());
        $this->historyRepository->flush();
    }
}
