<?php

namespace Trulyao\PhpRouter\Engines;


class TemplateEngine
{


    public static function render(string $file_name, $data = [], bool $as_template = false)
    {
        if($as_template) {
            self::render_with_template($file_name, $data);
        } else {
            self::render_without_template($file_name, $data);
        }
    }

    private static function render_without_template($file_name, $data)
    {
        extract($data);
        include $file_name;
    }

    public static function render_with_template(string $template, array $data = [])
    {
        $raw_data = $data;
        extract($data);
        ob_start();
        $page = file_get_contents($template);
        $formatted_page = self::extract_and_execute_raw_php(self::replace_template_variables(self::extract_and_render_component($page, $raw_data), $raw_data));
        echo $formatted_page;
        ob_get_contents();
        ob_end_flush();
    }

    public static function replace_template_variables(string $template, array $data = [])
    {
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
            $value = $data[$field_name] ?? null;
            foreach ($matches_array as $key) {
                if(is_array($value)) {
                    $value = $value[$key] ?? null;
                } else {
                    $value = null;
                }
            }

            return $value;

        }, $template);
    }

    public static function extract_and_execute_raw_php (string $template)
    {
        $regex = "/@php(.*?)@endphp/s";
        return preg_replace_callback($regex, function ($matches) {
            $php_code = $matches[1];
            return eval($php_code);
        }, $template);
    }

    public static function extract_and_render_component(string $template, array $data = [])
    {
        $root_dir = $data["root_dir"] ?? "";
        $regex = "/@component\((.*?)\)/m";
        return preg_replace_callback($regex, function ($matches) use ($data, $root_dir) {
            $component = $matches[1];
            $component_path = $root_dir."/$component";
            if(file_exists($component_path)) {
                return file_get_contents($component_path);
            } else {
                echo "<script>console.log('Component $component not found')</script>";
            }
        }, $template);
    }

}