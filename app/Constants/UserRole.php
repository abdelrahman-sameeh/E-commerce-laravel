<?php


namespace App\Constants;

class UserRole
{
  const USER = 0;
  const OWNER = 1;
  const ADMIN = 2;
  const DELIVERY = 3;

  public static array $roles = [
    self::USER => "user",
    self::OWNER => "owner",
    self::ADMIN => "admin",
    self::DELIVERY => "delivery",
  ];
}

