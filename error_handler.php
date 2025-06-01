<?php
class ErrorHandler {
    private $errors = [];

    public function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    public function displayErrors() {
        if ($this->hasErrors()) {
            echo '<div class="alert"><ul>';
            foreach ($this->errors as $error) {
                echo '<li>'.htmlspecialchars($error).'</li>';
            }
            echo '</ul></div>';
        }
    }
}
?>