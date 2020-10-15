<?php

namespace Guesl\CardConnect\CoPilot\Models;

class Attachment extends Model
{
    /**
     * Required: yes
     * size: 60
     *
     * @var string
     */
    protected $fileName;

    /**
     * Required: yes
     * size: 30
     *
     * @var string
     */
    protected $mimeType;

    /**
     * Required: yes
     * size: 1000
     *
     * @var string
     */
    protected $description;

    /**
     * https://developer.cardconnect.com/copilotapi#attachment-type-enumeration-definition
     *
     * Required: yes
     *
     * @var string
     */
    protected $attachmentTypeCd;

    /**
     * Required: yes
     * size 100Mb
     *
     * @var string Base 64 encoded string
     */
    protected $document;

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return Attachment
     */
    public function setFileName(string $fileName): Attachment
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return Attachment
     */
    public function setMimeType(string $mimeType): Attachment
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Attachment
     */
    public function setDescription(string $description): Attachment
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttachmentTypeCd(): string
    {
        return $this->attachmentTypeCd;
    }

    /**
     * @param string $attachmentTypeCd
     * @return Attachment
     */
    public function setAttachmentTypeCd(string $attachmentTypeCd): Attachment
    {
        $this->attachmentTypeCd = $attachmentTypeCd;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->document;
    }

    /**
     * @param string $document
     * @return Attachment
     */
    public function setDocument(string $document): Attachment
    {
        $this->document = $document;
        return $this;
    }
}
