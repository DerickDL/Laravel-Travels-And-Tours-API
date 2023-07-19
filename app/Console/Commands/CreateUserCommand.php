<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('What is the name of the user?');
        $user['email'] = $this->ask('What is the email of the user?');
        $user['password'] = $this->secret('What is the password of the user?');
        $confirmPassword = $this->secret('Please confirm the password of the user');

        if ($user['password'] !== $confirmPassword) {
            $this->error('Password does not match');
            return -1;
        }

        $validator = Validator::make($user, [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'unique:users,email', 'max:255', 'email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return -1;
        }

        $userRole = $this->choice(
            'Please choose a role for this user', 
            ['admin', 'editor'], 
            1);


        $role = Role::where('name', $userRole)->first();
        if (!$role) {
            $this->error('Role not found');
            return -1;
        }

        DB::transaction(function () use ($user, $role){
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);
        });

        $this->info('User created successfully');

    }
}
