<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\UploadedFile;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        UploadedFile::macro('storeOnCloudinary', function ($folder = null) {
            $response = Cloudinary::uploadApi()->upload($this->getRealPath(), [
                'folder' => $folder,
            ]);

            return new class($response) {
                public function __construct(public $response) {}

                public function getSecurePath()
                {
                    return $this->response['secure_url'];
                }

                public function getPublicId()
                {
                    return $this->response['public_id'];
                }
            };
        });
    }
}
