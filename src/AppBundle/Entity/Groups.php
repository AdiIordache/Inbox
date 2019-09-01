<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupsRepository")
 */
class Groups
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=255)
     */
    private $groupName;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     * @return Groups
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }


    /**
     * @var Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="groups")
     */
    private $persons;

    /**
     * @return Person[]
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * @param mixed $persons
     * @return Groups
     */
    public function setPersons($persons)
    {
        $this->persons = $persons;
        return $this;
    }

    public function __construct()
    {
        $this->persons = new ArrayCollection();
    }

    /**
     * @param Person $person
     */
    public function addPerson(Person $person)
    {
        if ($this->persons->contains($person)) {
            return;
        }
        $this->persons->add($person);
        $person->addPersonGroup($this);
    }

    /**
     * @param Person $person
     */
    public function removePerson(Person $person)
    {
        if (!$this->persons->contains($person)) {
            return;
        }
        $this->persons->removeElement($person);
        $person->removePersonGroup($this);
    }
}
