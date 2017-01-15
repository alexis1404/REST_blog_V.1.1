<?php

namespace AppBundle\Repository;

/**
 * UserGalleryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserGalleryRepository extends \Doctrine\ORM\EntityRepository
{
    public function saverObject($object)
    {
        $em = $this->getEntityManager();
        $em->persist($object);
        $em->flush();
    }

    public function removeObject($object)
    {
        $em = $this->getEntityManager();
        $em->remove($object);
        $em->flush();
    }
}