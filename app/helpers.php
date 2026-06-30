<?php

use Illuminate\Support\Arr;

if (!function_exists('media_url')) {
    function media_url($path)
    {
        if (!$path) return '';

        $path = is_array($path) ? Arr::first($path) : $path;

        $path = ltrim($path, '/');

        // remove wrong old prefixes or duplicate storage/ prefix
        $path = preg_replace('#^media/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        return rtrim(env('APP_URL', asset('')), '/') . '/storage/' . $path;
    }
}

if (!function_exists('parse_tiptap_html')) {
    function parse_tiptap_html($html)
    {
        if (empty($html) || !is_string($html)) return $html;
        
        return preg_replace_callback('/src="(?!(?:https?:\/\/|data:))([^"]+)"/i', function($matches) {
            $src = ltrim($matches[1], '/');
            return 'src="' . media_url($src) . '"';
        }, $html);
    }
}