<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class UserModel extends Model
{
    private string $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserById(int $id): ?object
    {
        return $this->find_single($this->table, $id);
    }

    public function getUserByUsername(string $username): ?object
    {
        return $this->find_single($this->table, null, '', [['username', '=', $username]]);
    }

    public function getUserByEmail(string $email): ?object
    {
        return $this->find_single($this->table, null, '', [['email', '=', $email]]);
    }

    public function authenticate(string $username, string $password): ?object
    {
        $user = $this->getUserByUsername($username);
        if ($user && $user->status === 1 && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public function createUser(array $data): int|bool
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->insert($this->table, $data);
    }

    public function updateUser(int $id, array $data): bool
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return $this->update($this->table, $data, $id);
    }

    public function deleteUser(int $id): bool
    {
        return $this->delete($this->table, $id);
    }

    public function getAllUsers(): array
    {
        return $this->find_all($this->table);
    }
}
