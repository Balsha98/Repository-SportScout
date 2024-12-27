<?php

class ReusableTemplate
{
    /**
     * Display a div in case nothing was selected.
     * @param mixed $data - assoc. array of data.
     * @return string the message div.
     */
    static function generate_none_selected_div($data)
    {
        return "
            <div class='div-none-selected-container'>
                <ion-icon class='none-selected-icon' name='alert-circle-outline'></ion-icon>
                <div class='none-selected-text'>
                    <h2>No {$data['section']} has been selected.</h2>
                    <p>Please select one of the {$data['message']}.</p>
                </div>
            </div>
        ";
    }

    /**
     * Display a div in case there are no rows available.
     * @param mixed $text - text to be displayed.
     * @return string the message div.
     */
    static function generate_none_available_div($text, $target)
    {
        return "
            <div class='div-none-available-container'>
                <ion-icon class='none-available-icon' name='alert-circle-outline'></ion-icon>
                <div class='none-available-text'>
                    <h2>No {$text}s at the moment.</h2>
                    <p>Start off by adding a <span>new</span> {$text} to the {$target}.</p>
                </div>
            </div>
        ";
    }

    /**
     * Adding the + button in the bottom right corner of a
     * section, for adding new rows of data into the database.
     * @param mixed $index - popup index.
     * @return string the + button.
     */
    static function generate_popup_add_btn($index = 0)
    {
        return "
            <div class='div-btn-add'>
                <button class='btn-add btn-show' data-popup-index='{$index}'>
                    <ion-icon class='btn-add-icon' name='add-outline'></ion-icon>
                </button>
            </div>
        ";
    }

    /**
     * Display button for showing the popup for editing.
     * @return string the edit button.
     */
    static function generate_popup_edit_btn()
    {
        return '
            <div class="div-btn-edit">
                <button class="btn-edit">
                    <ion-icon class="btn-edit-icon" name="create-outline"></ion-icon>
                </button>
            </div>
        ';
    }

    /**
     * Display buttons for submitting the form.
     * @return string the form submission buttons.
     */
    static function generate_form_submit_btns($target)
    {
        return "
            <div class='grid-btn-container'>
                <button class='btn btn-hollow btn-delete' type='submit' data-method='POST' data-clicked='DELETE_{$target}'>Delete</button>
                <button class='btn btn-full btn-update' type='submit' data-method='POST' data-clicked='UPDATE_{$target}'>Update</button>
            </div>
        ";
    }
}
