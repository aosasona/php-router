<?php

namespace Trulyao\PhpRouter\Engines;


class TemplateEngine
{
    public static function render(string $template, array $data = [])
    {
        $raw_data = $data;
        extract($data);
        ob_start();
        $page = file_get_contents($template);
        $formatted_page =  TemplateEngine::replace_template_variables($page, $raw_data);
        echo $formatted_page;
        ob_get_contents();
    }

    public static function replace_template_variables(string $template, array $data = [])
    {
        $data_regex = "/@data\((.*?)\)/m";
        $header_regex = "/@header\((.*?)\)/m";
        $query_regex = "/@query\((.*?)\)/m";
        $body_regex = "/@body\((.*?)\)/m";
        $template = preg_replace_callback($data_regex, function ($matches) use ($data) {
            $key = $matches[1];
            $matches_array = explode(".", $key);
            $value = $data["data"];
            foreach ($matches_array as $key) {
                $value = $value[$key];
            }
            return $value;


        }, $template);

        $template = preg_replace_callback($header_regex, function ($matches) use ($data) {
            $key = strtolower($matches[1]);
            $matches_array = explode(".", $key);
            $value = $data["headers"];
            foreach ($matches_array as $key) {
                $value = $value[$key];
            }
            return $value;
        }, $template);

        return $template;
    }

}