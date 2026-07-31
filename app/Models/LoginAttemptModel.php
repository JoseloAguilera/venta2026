<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table            = 'login_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ip_address', 'username', 'attempts', 'last_attempt'];

    public function recordFailedAttempt($ipAddress, $username)
    {
        $existing = $this->where('ip_address', $ipAddress)
                         ->where('username', $username)
                         ->first();

        if ($existing) {
            $this->update($existing['id'], [
                'attempts'     => $existing['attempts'] + 1,
                'last_attempt' => date('Y-m-d H:i:s')
            ]);
            return $existing['attempts'] + 1;
        } else {
            $this->insert([
                'ip_address'   => $ipAddress,
                'username'     => $username,
                'attempts'     => 1,
                'last_attempt' => date('Y-m-d H:i:s')
            ]);
            return 1;
        }
    }

    public function isLockedOut($ipAddress, $username, $maxAttempts = 5, $lockoutMinutes = 15)
    {
        $existing = $this->where('ip_address', $ipAddress)
                         ->where('username', $username)
                         ->first();

        if (!$existing || $existing['attempts'] < $maxAttempts) {
            return false;
        }

        $lastAttemptTime = strtotime($existing['last_attempt']);
        $timeDiff = (time() - $lastAttemptTime) / 60;

        if ($timeDiff >= $lockoutMinutes) {
            $this->delete($existing['id']);
            return false;
        }

        return true;
    }

    public function resetAttempts($ipAddress, $username)
    {
        $this->where('ip_address', $ipAddress)
             ->where('username', $username)
             ->delete();
    }
}
