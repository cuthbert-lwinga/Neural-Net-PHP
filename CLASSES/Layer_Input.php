<?PHP

class Layer_Input {
    public $prev;
    public $next;
    public $output;

    public function forward($inputs) {
        $this->output = $inputs;
    }
}

?>