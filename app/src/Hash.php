<?php namespace src;

class Hash
{
    protected $hashAlgo = '';
    protected $hashCost = '';

    public function __construct($hashAlgo, $hashCost)
    {
        $this->hashAlgo = $hashAlgo;
        $this->hashCost = $hashCost;
    }

    public function password($password)
    {
        return password_hash(
            $password,
            $this->hashAlgo,
            ['cost' => $this->hashCost]
        );
    }

    public function passwordCheck($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function hash($input)
    {
        return hash('sha256', $input);
    }

    public function hashCheck($known, $user)
    {
        if (!function_exists('hash_equals')) {
            return $this->hashEquals($known, $user);
        }

        return hash_equals($known, $user);
    }

    private function hashEquals($a, $b)
    {
        $ret = strlen($a) ^ strlen($b);
        $ret |= array_sum(unpack("C*", $a^$b));
        
        return !$ret;
    }
}
