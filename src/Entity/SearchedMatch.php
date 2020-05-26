<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SearchedMatchRepository")
 */
class SearchedMatch
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
     * @ORM\ManyToMany(targetEntity="User")
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
     * @return SearchedMatch
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
     * @return SearchedMatch
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
     * @return SearchedMatch
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
     * @return SearchedMatch
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
     * @return SearchedMatch
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param User $player
     */
    public function addPlayer(User $player)
    {
        if ($this->team1Roster->contains($player)) {
            return;
        }

        $this->team1Roster->add($player);
    }

    /**
     * @param User $player
     */
    public function removePlayer(User $player)
    {
        if (!$this->team1Roster->contains($player)) {
            return;
        }

        $this->team1Roster->removeElement($player);
    }

    /**
     * @param User $user
     * @param User $searchedUser
     *
     * @return array
     */
    public function toArray(User $user, User $searchedUser): array
    {
        $isOnTeam1 = false;
        foreach ($this->team1Roster->getValues() as $player) {
            /** @var User $player */
            if ($player->getGuid() === $user->getGuid()) {
                $isOnTeam1 = true;
                break;
            }
        }

        return [
            'team1' => $this->team1,
            'team2' => $this->team2,
            'winner' => $this->winner,
            'userTeam' => $isOnTeam1 ? 'team1' : 'team2',
            'searchedTeam' => $this->team1Roster->contains($searchedUser) ? 'team1' : 'team2',
            'url' => $this->url
        ];
    }
}