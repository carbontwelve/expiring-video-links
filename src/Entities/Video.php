<?php

namespace App\Entities;

/**
 * @Entity
 * @Table(name="videos", uniqueConstraints={@UniqueConstraint(name="video_code__unique",columns={"code"})})
 * @Entity(repositoryClass="App\Repositories\VideoRepository")
 */
class Video
{
    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;

    /**
     * @var float
     * @Column(type="float")
     */
    private $created;

    /**
     * @var float
     * @Column(type="float", nullable = true)
     */
    private $modified;

    /**
     * @var string
     * @Column(type="text")
     */
    private $code;

    /**
     * @var string
     * @Column(type="text")
     */
    private $videoFile;

    /**
     * @var integer
     * @Column(type="integer")
     */
    private $watches;

    /**
     * @var integer
     * @Column(type="integer")
     */
    private $maxWatches;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param float $created
     */
    public function setCreated(float $created): void
    {
        $this->created = $created;
    }

    /**
     * @param float $modified
     */
    public function setModified(float $modified): void
    {
        $this->modified = $modified;
    }

    /**
     * @param string $text
     */
    public function setCode(string $text): void
    {
        $this->code = $text;
    }

    /**
     * @return string
     */
    public function getVideoFile(): string
    {
        return $this->videoFile;
    }

    /**
     * @param string $videoFile
     */
    public function setVideoFile(string $videoFile): void
    {
        $this->videoFile = $videoFile;
    }

    /**
     * @return int
     */
    public function getWatches(): int
    {
        return $this->watches;
    }

    /**
     * @param int $watches
     */
    public function setWatches(int $watches): void
    {
        $this->watches = $watches;
    }

    /**
     * @return int
     */
    public function getMaxWatches(): int
    {
        return $this->maxWatches;
    }

    /**
     * @param int $maxWatches
     */
    public function setMaxWatches(int $maxWatches): void
    {
        $this->maxWatches = $maxWatches;
    }
}