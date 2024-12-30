<?php

use App\Models\Order;
use App\Models\User;

function getFileName($file): string
{
    return time().'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
}
function getEmailName($email): string
{
    // Use explode to split the email into two parts: before and after the '@'
    $parts = explode('@', $email);

    // Return the first part, which is the username
    return $parts[0];
}

function generateUniqueUsername($name): array|string|null
{
    $baseUsername = strtolower($name);
    $username = $baseUsername;
    $count = 1;
    while (User::where('user_name', $username)->exists()) {
        $username = $baseUsername.$count;
        $count++;
    }

    return $username;
}

function formatNumber($number, $precision = 2): array
{
    if ($number >= 1000000000000000) {
        return [
            'number' => number_format($number / 1000000000000000, $precision),
            'format' => 'Q',
        ];
    } elseif ($number >= 1000000000000) {
        return [
            'number' => number_format($number / 1000000000000, $precision),
            'format' => 'T',
        ];
    } elseif ($number >= 1000000000) {
        return [
            'number' => number_format($number / 1000000000, $precision),
            'format' => 'B',
        ];
    } elseif ($number >= 1000000) {
        return [
            'number' => number_format($number / 1000000, $precision),
            'format' => 'M',
        ];
    } elseif ($number >= 1000) {
        return [
            'number' => number_format($number / 1000, $precision),
            'format' => 'K',
        ];
    }

    // For numbers less than 1K, no format suffix is needed
    return [
        'number' => number_format($number),
        'format' => '',
    ];
}

if (! function_exists('is_url')) {
    /**
     * Check if a given string is a valid URL.
     *
     * @param  string  $url
     * @return bool
     */
    function is_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
if (! function_exists('generateOrderNumber')) {
    function generateOrderNumber(): string
    {
        $latestOrderNumber = Order::latest()->first();
        do {
            $orderNumber = 'ORD-'.now()->year.'-'.str_pad(($latestOrderNumber?->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);
            $exists = Order::where('order_number', $orderNumber)->exists();
        } while ($exists);

        return $orderNumber;
    }
}

if (! function_exists('mask_email')) {
    /**
     * Mask an email address like 'max****@gmail.com'.
     *
     * @param string $email
     */
    function mask_email(string $email): string
    {
        $email_parts = explode('@', $email);
        $name_part = $email_parts[0];
        $domain_part = '@'.$email_parts[1];

        // Mask the email by revealing the first three characters and adding stars
        $name_length = strlen($name_part);
        $mask_length = $name_length > 3 ? $name_length - 3 : 1;
        $masked_name = substr($name_part, 0, 3).str_repeat('*', $mask_length);

        return $masked_name.$domain_part;
    }


    // Helper function to get hex code for color (if you're using standard colors)
    function hexColorForStatus($color) {
        $colorHexMap = [
            'yellow' => '00a1ff',
            'blue' => '0000FF',
            'orange' => 'FFA500',
            'green' => '008000',
            'red' => 'FF0000',
            'gray' => '808080',
        ];

        return $colorHexMap[$color] ?? '808080'; // Default to gray if color not found
    }
}
