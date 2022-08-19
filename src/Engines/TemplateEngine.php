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
        $formatted_page = self::replace_template_variables($page, $raw_data);
        echo $formatted_page;
        ob_get_contents();
    }

    public static function replace_template_variables(string $template, array $data = [])
    {
        $data_regex = "/@data\((.*?)\)/m";
        $available_fields = array_keys($data);

        foreach ($available_fields as $field) {
            $field_regex = "/@{$field}\((.*?)\)/m";
            $template = self::extract_and_replace_variables_per_directive($field_regex, $data, $template, $field);
        }

        return $template;
    }

    /**
     * @param string $regex
     * @param array $data
     * @param string $template
     * @param string $field_name
     * @return array|string|string[]|null
     */
    public static function extract_and_replace_variables_per_directive(string $regex, array $data, string $template, string $field_name)
    {
        return preg_replace_callback($regex, function ($matches) use ($field_name, $data) {
            $key = $matches[1];
            $matches_array = explode(".", $key);
            $value = $data[$field_name];
            foreach ($matches_array as $key) {
                $value = $value[$key];
            }
            return $value;


        }, $template);
    }

}