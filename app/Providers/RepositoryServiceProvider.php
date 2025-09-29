<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//Interfaces
use App\Interfaces\User\{
    UserRepositoryInterface, KycRepositoryInterface, UserDetailsRepositoryInterface , UserDocumentRepositoryInterface, UserWalletsRepositoryInterface
};
use App\Interfaces\Admin\{
    AdminRepositoryInterface, RoleRepositoryInterface
};
use App\Interfaces\{
    MailTemplatesRepositoryInterface ,CountryRepositoryInterface, StateRepositoryInterface, CityRepositoryInterface, ContestsRepositoryInterface
};


//Repositories
use App\Repositories\User\{
    UserRepository, KycRepository, UserDetailsRepository, UserDocumentRepository, UserWalletsRepository
};
use App\Repositories\Admin\{
    AdminRepository, RoleRepository
};
use App\Repositories\{
    MailTemplatesRepository, CountryRepository, StateRepository, CityRepository, ContestsRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //Comman
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(MailTemplatesRepositoryInterface::class, MailTemplatesRepository::class);
        $this->app->bind(ContestsRepositoryInterface::class, ContestsRepository::class);


        //Admin
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);


        //User
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(KycRepositoryInterface::class, KycRepository::class);
        $this->app->bind(UserDetailsRepositoryInterface::class, UserDetailsRepository::class);
        $this->app->bind(UserDocumentRepositoryInterface::class, UserDocumentRepository::class);
        $this->app->bind(UserWalletsRepositoryInterface::class, UserWalletsRepository::class);
        
        
                
    }


    public function boot(): void
    {
        //
    }
}