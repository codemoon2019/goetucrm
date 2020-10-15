<?php

namespace Guesl\CardConnect\CoPilot\Models;

class SignatureStatus extends Model
{
    /**
     * @var string
     */
    protected $signatureUrl;

    /**
     * @var string
     */
    protected $signedSiteUserId;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $signedDateTime;

    /**
     * MM/DD/YYYY HH:II:SS a
     * "m/d/Y H:i:s a"
     *
     * Like "10/21/2013 08:18:18 AM".
     *
     * @var string
     */
    protected $signer;

    /**
     * The signatureStatusCd will be one of the following: NOT_SENT, PENDING, or SIGNED.
     *
     * NOT_SENT: represents a merchant that is not ready for the owner to sign the application and an email has not been sent to the owner.
     * PENDING: represents a signature request has been sent to the owner.
     * SIGNED: means the owner has signed the application.
     *
     * @var string
     */
    protected $signatureStatusCd;

    /**
     * @return string
     */
    public function getSignatureUrl(): string
    {
        return $this->signatureUrl;
    }

    /**
     * @param string $signatureUrl
     * @return SignatureStatus
     */
    public function setSignatureUrl(string $signatureUrl): SignatureStatus
    {
        $this->signatureUrl = $signatureUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignedSiteUserId(): string
    {
        return $this->signedSiteUserId;
    }

    /**
     * @param string $signedSiteUserId
     * @return SignatureStatus
     */
    public function setSignedSiteUserId(string $signedSiteUserId): SignatureStatus
    {
        $this->signedSiteUserId = $signedSiteUserId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignedDateTime(): string
    {
        return $this->signedDateTime;
    }

    /**
     * @param string $signedDateTime
     * @return SignatureStatus
     */
    public function setSignedDateTime(string $signedDateTime): SignatureStatus
    {
        $this->signedDateTime = $signedDateTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getSigner(): string
    {
        return $this->signer;
    }

    /**
     * @param string $signer
     * @return SignatureStatus
     */
    public function setSigner(string $signer): SignatureStatus
    {
        $this->signer = $signer;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureStatusCd(): string
    {
        return $this->signatureStatusCd;
    }

    /**
     * @param string $signatureStatusCd
     * @return SignatureStatus
     */
    public function setSignatureStatusCd(string $signatureStatusCd): SignatureStatus
    {
        $this->signatureStatusCd = $signatureStatusCd;
        return $this;
    }
}
