<?php

class ImageProcessor
{
    private $image;

    // Constructor
    public function __construct($imagePath) {
        $this->loadImage($imagePath);
        return $this;
    }

    // Destructor to free up resources
    public function __destruct() {
        if ($this->image !== null) {
            imagedestroy($this->image);
        }
    }

    private function fileExists($filePath) {
        return file_exists($filePath);
    }

    // Load image from file
    private function loadImage($imagePath) {
        $imageType = exif_imagetype($imagePath);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($imagePath);
                break;
            // Add more formats as needed
            default:
                throw new Exception("Unsupported image type");
        }
    }

    // Example function: Save image
    public function saveImage($savePath, $imageType = IMAGETYPE_PNG) {
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $savePath);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $savePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $savePath);
                break;
            // Add more formats as needed
            default:
                throw new Exception("Unsupported image type");
        }
    }

    // Function to get RGB color channels
    public function getColorChannels() {
        $width = imagesx($this->image);
        $height = imagesy($this->image);

        // Create images for each color channel
        $red = imagecreatetruecolor($width, $height);
        $green = imagecreatetruecolor($width, $height);
        $blue = imagecreatetruecolor($width, $height);

        // Extract each color channel
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($this->image, $x, $y);
                $colors = imagecolorsforindex($this->image, $rgb);

                // Set pixel for the red channel
                $redColor = imagecolorallocate($red, $colors['red'], 0, 0);
                imagesetpixel($red, $x, $y, $redColor);

                // Set pixel for the green channel
                $greenColor = imagecolorallocate($green, 0, $colors['green'], 0);
                imagesetpixel($green, $x, $y, $greenColor);

                // Set pixel for the blue channel
                $blueColor = imagecolorallocate($blue, 0, 0, $colors['blue']);
                imagesetpixel($blue, $x, $y, $blueColor);
            }
        }

        // Return the color channel images
        return [$red,$green,$blue];
    }

    public function combineColorChannels($channels) {
        // Extract the individual color channels from the array
        $redChannel = $channels[0];
        $greenChannel = $channels[1];
        $blueChannel = $channels[2];

        $width = imagesx($redChannel);
        $height = imagesy($redChannel);

        $rgbArray = [];

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $red = imagecolorat($redChannel, $x, $y) >> 16 & 0xFF;
                $green = imagecolorat($greenChannel, $x, $y) >> 8 & 0xFF;
                $blue = imagecolorat($blueChannel, $x, $y) & 0xFF;

                $rgbArray[$x][$y] = ['red' => $red, 'green' => $green, 'blue' => $blue];
            }
        }

        return $rgbArray;
    }


public function getImageRGB() {
        $width = imagesx($this->image);
        $height = imagesy($this->image);
        $rgbArray = array();

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($this->image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $rgbArray[] = [$r,$g,$b];
            }
        }

        return $rgbArray;
    }

    public function getImageGrayscaleArray($dimensions,$grayScale = true) {
        // Create a 2D array with the given dimensions filled with zeros.
        $grayscaleArray = array_fill(0, $dimensions['rows'], array_fill(0, $dimensions['cols'], 0));

        // Assuming $this->image contains the RGB array.
        $rgbArray = $this->getImageRGB(); // Method to get the RGB array.

        // Calculate the grayscale value for each pixel.
        for ($row = 0; $row < $dimensions['rows']; $row++) {
            for ($col = 0; $col < $dimensions['cols']; $col++) {
                // Get the index for the 1D RGB array based on the current row and column.
                $index = ($row * $dimensions['cols']) + $col;
                
                // Check if the index exists in the RGB array to avoid undefined offset.
                if (isset($rgbArray[$index])) {
                    // Convert to grayscale using the luminosity method.
                    $grayscaleValue = $grayScale? $rgbArray[$index][2]:round(($rgbArray[$index][0] * 0.3) + ($rgbArray[$index][1] * 0.59) + ($rgbArray[$index][2] * 0.11));
                    $grayscaleArray[$row][$col] = $grayscaleValue;
                }
            }
        }

        return $grayscaleArray;
    }


    public function printGrayscaleArray($grayscaleArray) {
        $maxLength = $this->getMaxNumberLength($grayscaleArray);

        foreach ($grayscaleArray as $row) {
            foreach ($row as $value) {
                printf("%{$maxLength}s ", $value);
            }
            echo PHP_EOL; // New line at the end of each row.
        }
    }

    private function getMaxNumberLength($grayscaleArray) {
        $maxLen = 0;
        foreach ($grayscaleArray as $row) {
            foreach ($row as $value) {
                $len = strlen((string)$value);
                if ($len > $maxLen) {
                    $maxLen = $len;
                }
            }
        }
        return $maxLen;
    }


}

?>
