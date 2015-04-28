<?php

namespace Hart\Twig;

class fbAvatarExtension extends \Twig_Extension
{
    public function getName() {
        return "fb_avatar";
    }

    public function getFilters() {
        return array(
            "fb_avatar" => new \Twig_Filter_Method($this, "getFbAvatar"),
        );
    }

    public function getFbAvatar($input) {

        if(is_numeric($input)) {
            $input = "http://graph.facebook.com/$input/picture";
        }

        return $input;
    }
}