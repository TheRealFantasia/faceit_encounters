<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CachedMatchRepository")
 */
class CachedMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $guid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team2;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $winner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToMany(targetEntity="CachedName")
     * @ORM\JoinTable(name="match_players",
     *      joinColumns={@ORM\JoinColumn(name="match_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")}
     * )
     */
    private $team1Roster;

    public function __construct()
    {
        $this->team1Roster = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param mixed $guid
     *
     * @return CachedMatch
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * @param mixed $team1
     *
     * @return CachedMatch
     */
    public function setTeam1($team1)
    {
        $this->team1 = $team1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * @param mixed $team2
     *
     * @return CachedMatch
     */
    public function setTeam2($team2)
    {
        $this->team2 = $team2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @param mixed $winner
     *
     * @return CachedMatch
     */
    public function setWinner($winner)
    {
        $this->winner = $winner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return CachedMatch
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param CachedName $player
     */
    public function addPlayer(CachedName $player)
    {
        if ($this->team1Roster->contains($player)) {
            return;
        }

        $this->team1Roster->add($player);
    }

    /**
     * @param CachedName $player
     */
    public function removePlayer(CachedName $player)
    {
        if (!$this->team1Roster->contains($player)) {
            return;
        }

        $this->team1Roster->removeElement($player);
    }

    /**
     * @param string $own
     * @param CachedName $other
     *
     * @return array
     */
    public function toArray(string $own, CachedName $other): array
    {
        $isOnTeam1 = false;
        foreach ($this->team1Roster->getValues() as $player) {
            /** @var CachedName $player */
            if ($player->getFaceitId() === $own) {
                $isOnTeam1 = true;
                break;
            }
        }

        return [
            'team1' => $this->team1,
            'team2' => $this->team2,
            'winner' => $this->winner,
            'ownTeam' => $isOnTeam1 ? 'team1' : 'team2',
            'otherTeam' => $this->team1Roster->contains($other) ? 'team1' : 'team2',
            'url' => $this->url
        ];
    }
}