<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Resource\Common\BaseResource;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class UploadController extends AbstractController
{
    public function upload(RequestInterface $request, ResponseInterface $response)
    {
        $file = $request->file('file');
        if (!$file) {
            throw new BusinessException(ErrorCode::NO_FILE);
        }

        $date = date('Ymd');
        $basePath = BASE_PATH . '/storage/uploads/' . $date;
        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        $filename = uniqid('', true) . '.' . $file->getExtension();
        $targetPath = $basePath . '/' . $filename;

        $file->moveTo($targetPath);

        if (!$file->isMoved()) {
            throw new BusinessException(ErrorCode::NO_FILE);
        }

        $url = '/uploads/' . $date . '/' . $filename;

        return new BaseResource(['url' => $url]);
    }
}
