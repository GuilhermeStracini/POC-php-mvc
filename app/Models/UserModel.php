<?php

namespace GuiBranco\PocMvc\App\Models;

class UserModel
{
    // Mock data to simulate a database
    private static $users = [
        1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com'],
        2 => ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane.doe@example.com'],
        3 => ['id' => 3, 'name' => 'Alice', 'email' => 'alice@example.com'],
        4 => ['id' => 4, 'name' => 'Bob', 'email' => 'bob@example.com'],
        5 => ['id' => 5, 'name' => 'Charlie', 'email' => 'charlie@example.com']
    ];

    private static $nextId = 6;


    // Fetch all users
    public static function all()
    {
        return self::$users;
    }

    // Find a user by ID
    public static function find($id)
    {
        return self::$users[$id] ?? null;
    }

    // Create a new user
    public static function create(array $data)
    {
        $userId = self::$nextId++;
        $data['id'] = $userId;
        self::$users[$userId] = $data;

        return self::$users[$userId];
    }

    // Update a user
    public static function update($id, array $data)
    {
        if (isset(self::$users[$id])) {
            self::$users[$id] = array_merge(self::$users[$id], $data);
            return self::$users[$id];
        }

        return null;
    }

    // Delete a user
    public static function delete($id)
    {
        if (isset(self::$users[$id])) {
            unset(self::$users[$id]);
            return true;
        }

        return false;
    }
}
