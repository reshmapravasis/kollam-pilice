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
    function parse_tiptap_html($html, $tableSize = 'table-md')
    {
        if (empty($html) || !is_string($html)) return $html;
        
        $html = preg_replace_callback('/src="(?!(?:https?:\/\/|data:))([^"]+)"/i', function($matches) {
            $src = ltrim($matches[1], '/');
            return 'src="' . media_url($src) . '"';
        }, $html);

        // Wrap tables in a responsive div with size class
        $html = preg_replace_callback('/<table([^>]*)>(.*?)<\/table>/is', function ($matches) use ($tableSize) {
            return '<div class="table-responsive ' . e($tableSize) . '"><table' . $matches[1] . '>' . $matches[2] . '</table></div>';
        }, $html);

        return $html;
    }
}