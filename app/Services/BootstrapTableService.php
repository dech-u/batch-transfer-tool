<?php

namespace App\Services;

class BootstrapTableService
{
    private static string $defaultClasses = "btn btn-xs btn-rounded btn-icon";

    /**
     * @param string $iconClass
     * @param string $url
     * @param array $customClass
     * @param array $customAttributes
     * @return string
     */
    public static function button(string $iconClass, string $url, array $customClass = [], array $customAttributes = [])
    {
        $customClassStr = implode(" ", $customClass);
        $class = self::$defaultClasses . ' ' . $customClassStr;
        $attributes = '';
        if (count($customAttributes) > 0) {
            foreach ($customAttributes as $key => $value) {
                $attributes .= $key . '="' . $value . '" ';
            }
        }
        return '<a href="' . $url . '" class="' . $class . '" ' . $attributes . '><i class="' . $iconClass . '"></i></a>&nbsp;&nbsp;';
    }

    /**
     * @param $url
     * @param bool $modal
     * @return string
     */
    public static function editButton($url, bool $modal = true)
    {
        $customClass = ["edit-data", "btn-gradient-primary"];
        $customAttributes = [
            "title" => trans("Edit")
        ];
        if ($modal) {
            $customAttributes = [
                "title" => "Edit",
                "data-toggle" => "modal",
                "data-target" => "#editModal"
            ];

            $customClass[] = "set-form-url";
        }

        $iconClass = "fa fa-edit";
        return self::button($iconClass, $url, $customClass, $customAttributes);
    }

    /**
     * @param $url
     * @return string
     */
    public static function deleteButton($url)
    {
        $customClass = ["delete-form", "btn-gradient-dark"];
        $customAttributes = [
            "title" => trans("Delete"),
        ];
        $iconClass = "fa fa-trash";
        return self::button($iconClass, $url, $customClass, $customAttributes);
    }

    /**
     * @param $url
     * @param string $title
     * @return string
     */
    public static function restoreButton($url, string $title = "Restore")
    {
        $customClass = ["btn-gradient-success", "restore-data"];
        $customAttributes = [
            "title" => trans($title),
        ];
        $iconClass = "fa fa-refresh";
        return self::button($iconClass, $url, $customClass, $customAttributes);
    }

    /**
     * @param $url
     * @return string
     */
    public static function trashButton($url)
    {
        $customClass = ["btn-gradient-danger", "trash-data"];
        $customAttributes = [
            "title" => trans("Delete Permanent"),
        ];
        $iconClass = "fa fa-times";
        return self::button($iconClass, $url, $customClass, $customAttributes);
    }


    /**
     * @param $url
     * @return string
     */
    public static function viewRelatedDataButton($url,  bool $modal = true) {
        $customClass = ["edit-data", "btn-gradient-primary"];
        $customAttributes = [
            "title" => trans("View Related Data")
        ];
        if ($modal) {
            $customAttributes = [
                "title" => "Edit",
                "data-toggle" => "modal",
                "data-target" => "#editModal"
            ];

            $customClass[] = "set-form-url";
        }

        $iconClass = "fa fa-eye";
        return self::button($iconClass, $url, $customClass, $customAttributes);
    }

    // Menu list

    public static function menuButton($title, $url, $customClass = [], $customAttributes = [])
    {
        $attributes = '';
        $customClassStr = implode(" ", $customClass);
        if (count($customAttributes) > 0) {
            foreach ($customAttributes as $key => $value) {
                $attributes .= $key . '="' . $value . '" ';
            }
        }
        return '<a href="' . $url . '" class="dropdown-item p-2 ' . $customClassStr . '" ' . $attributes . '>'. trans($title) .'</a>';
    }

    public static function menuEditButton($title, $url, bool $modal = true)
    {
        $customClass = ["edit-data"];
        $customAttributes = [];
        if ($modal) {
            $customAttributes = [
                "data-toggle" => "modal",
                "data-target" => "#editModal"
            ];

            $customClass[] = " set-form-url";
        }

        return self::menuButton($title, $url, $customClass, $customAttributes);
    }

    public static function menuDeleteButton($title, $url)
    {
        $customClass = ["delete-form"];
        $customAttributes = [
            "title" => trans("Delete"),
        ];
        return self::menuButton($title, $url, $customClass, $customAttributes);
    }

    public static function menuRestoreButton($title, $url)
    {
        $customClass = ["restore-data"];
        $customAttributes = [];
        return self::menuButton($title, $url, $customClass, $customAttributes);
    }

    public static function menuTrashButton($title, $url)
    {
        $customClass = ["trash-data"];
        $customAttributes = [];
        return self::menuButton($title, $url, $customClass, $customAttributes);
    }

    public static function menuItem($operate)
    {

        // return '<div class="dropdown"> <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Dropdown </button> <div class="dropdown-menu" aria-labelledby="dropdownMenu2"> '. $operate .' </div> </div>';

        return '<div class="dropdown table-action-column d-flex align-items-center"> <button class="btn btn-sm btn-inverse-dark d-flex align-items-center" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-ellipsis-v"></i> </button> <div class="dropdown-menu action-column-dropdown-menu" aria-labelledby="dropdownMenuButton"> '. $operate .' </div> </div>';
    }

    /**
     * @param $url
     * @return string
     */
    public static function downloadButton($urls) {
        $customClass = ["related-data-form", "btn-inverse-primary"];
        $customAttributes = [
            "title" => trans("database_download"),
        ];
        $iconClass = "fa fa-download";
        return self::download_urls($iconClass, $urls, $customClass, $customAttributes);
    }
    
    public static function download_urls(string $iconClass, array $urls, array $customClass = [], array $customAttributes = []) {

        $customClassStr = implode(" ", $customClass);
        $class = self::$defaultClasses . ' ' . $customClassStr;
        $attributes = '';
        if (count($customAttributes) > 0) {
            foreach ($customAttributes as $key => $value) {
                $attributes .= $key . '="' . $value . '" ';
            }
        }

        return '<a href="' . $urls[0] . '" class="' . $class . '" ' . $attributes . ' ><i class="' . $iconClass . '"></i></a>  <a href="' . $urls[1] . '" class="' . $class . '" ' . $attributes . ' ><i class="fa fa-image"></i></a>  ';

    }

}
