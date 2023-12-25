<?php
class LinePlotter {
    private $width;
    private $height;
    private $image;
    private $colors = [];
    private $assignedColors = [];

    public function __construct($width = 400, $height = 400) {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        // Set a default background color to white
        $bgColor = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $bgColor);
    }

private function normalizePoints($points) {
        $minX = PHP_INT_MAX;
        $maxX = PHP_INT_MIN;
        $minY = PHP_INT_MAX;
        $maxY = PHP_INT_MIN;

        // Find min and max values for X and Y
        foreach ($points as $point) {
            $minX = min($minX, $point[0]);
            $maxX = max($maxX, $point[0]);
            $minY = min($minY, $point[1]);
            $maxY = max($maxY, $point[1]);
        }

        // Normalize points to fit within image bounds
        $normalizedPoints = [];
        foreach ($points as $point) {
            $normalizedX = ($point[0] - $minX) / ($maxX - $minX) * $this->width;
            $normalizedY = ($point[1] - $minY) / ($maxY - $minY) * $this->height;
            $normalizedPoints[] = [$normalizedX, $normalizedY];
        }

        return $normalizedPoints;
    }

    // Define colors
    public function setColor($name, $red, $green, $blue) {
        $this->colors[$name] = imagecolorallocate($this->image, $red, $green, $blue);
    }

    private function getRandomColor() {
        return imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
    }


public function plotLine($yValues, $colorName) {
    // Normalize the y-values first
    $minY = min($yValues);
    $maxY = max($yValues);
    
    $normalizedYValues = [];
    foreach ($yValues as $y) {
        $normalizedY = ($this->height - 10) - ($y - $minY) / ($maxY - $minY) * ($this->height - 20); // 10 padding from top and bottom
        $normalizedYValues[] = $normalizedY;
    }

    $step = $this->width / (count($normalizedYValues) - 1);
    $prevX = 10; // Start 10 pixels from the left for padding
    $prevY = $normalizedYValues[0];

    for ($i = 1; $i < count($normalizedYValues); $i++) {
        $x = $prevX + $step;
        imageline($this->image, $prevX, $prevY, $x, $normalizedYValues[$i], $this->colors[$colorName]);
        $prevX = $x;
        $prevY = $normalizedYValues[$i];
    }
}

public function plotMultipleLinesWithYOnly($lines) {
    foreach ($lines as $line) {
        if (!isset($line['yValues'], $line['color'])) {
            continue; // Skip if essential data is missing
        }

        $minY = min($line['yValues']);
        $maxY = max($line['yValues']);
        $normalizedYValues = array_map(function($y) use ($minY, $maxY) {
            if ($maxY - $minY == 0) {
                return ($this->height - 10) - ($this->height - 20) / 2;
            } else {
                return ($this->height - 10) - ($y - $minY) / ($maxY - $minY) * ($this->height - 20);
            }
        }, $line['yValues']);

        $countY = count($line['yValues']);
        // Handle the x-values independently for each line
        if ($countY > 1) {
            $step = ($this->width - 20) / ($countY - 1);
        } else {
            // If there is only one point, place it in the middle
            $step = ($this->width - 20) / 2;
        }

        $xValues = array_map(function($index) use ($step) {
            return 10 + $index * $step;
        }, range(0, $countY - 1));

        // Plot the line
        $this->plotLineWithColor($xValues, $normalizedYValues, $line['color']);
    }
}


public function plotLineWithColor($xValues, $yValues, $colorName) {
    // Normalize the values first
    $minX = min($xValues);
    $maxX = max($xValues);
    $minY = min($yValues);
    $maxY = max($yValues);

    $normalizedXValues = array_map(function($x) use ($minX, $maxX) {
        return ($x - $minX) / ($maxX - $minX) * ($this->width - 20) + 10; // 10 padding from left and right
    }, $xValues);

    $normalizedYValues = array_map(function($y) use ($minY, $maxY) {
        if ($maxY - $minY == 0) {
            // If all y-values are the same, normalize to the middle of the plot
            return ($this->height - 10) - ($this->height - 20) / 2;
        } else {
            return ($this->height - 10) - ($y - $minY) / ($maxY - $minY) * ($this->height - 20); // 10 padding from top and bottom
        }
    }, $yValues);

    for ($i = 0; $i < count($normalizedXValues) - 1; $i++) {
        imageline($this->image, $normalizedXValues[$i], $normalizedYValues[$i], 
                  $normalizedXValues[$i + 1], $normalizedYValues[$i + 1], $this->colors[$colorName]);
    }
}




public function plotPoints($points, $groups) {
    // Normalize the points first
    $normalizedPoints = $this->normalizePoints($points);

    $uniqueGroups = array_unique($groups);
    foreach ($uniqueGroups as $group) {
        if (!isset($this->assignedColors[$group])) {
            $this->assignedColors[$group] = $this->getRandomColor();
        }
    }

    for ($i = 0; $i < count($normalizedPoints); $i++) {
        $x = $normalizedPoints[$i][0];
        $y = $normalizedPoints[$i][1];
        $group = $groups[$i];
        imagefilledellipse($this->image, $x, $y, 4, 4, $this->assignedColors[$group]);
    }
}

    public function clearCanvas() {
        imagedestroy($this->image); // Destroy the current image resource
        $this->image = imagecreatetruecolor($this->width, $this->height);
        // Set a default background color to white
        $bgColor = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $bgColor);
    }

    public function save($filename) {
        imagepng($this->image, $filename);
        $this->clearCanvas();
    }

    public function __destruct() {
        imagedestroy($this->image);
    }
}


?>