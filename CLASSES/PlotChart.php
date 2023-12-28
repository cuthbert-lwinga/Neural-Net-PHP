<?php
/****
 * plotChart Class draw chart and plot data with various options.
 * Author: Win Aung Cho
 * Contact winaungcho@gmail.com
 * version 1.0
 * Date: 26-02-2023
 *
 ******/
class PlotChart
{
    function __construct($setting = array())
    {
        $this->im = null;
    	$this->imgw = 600;
        $this->imgh = 400;
        $this->mgright = 50;
        $this->mgtop = 80;
        $this->mgleft = $this->mgbot = 80;
        $this->tgrid = 15;
        $this->tstitle = 20;
        $this->ttitle = 25;
        $this->psize = 10;
        $this->ngx = 10;
        $this->ngy = 10;
        $this->text_rgb = '#649696';
        $this->title_rgb = '0000FA';
        $this->stitle_rgb = '009696';
        $this->line_rgb = 'E90E5B';
        $this->grid_rgb = '808080';
        $this->axis_rgb = '000000';
        $this->back_rgb = 'FAFAFA';
        $this->chart_rgb = 'EDEDED';

        $this->strtitle = "This is TITLE";
        $this->strstitle = "Subtitle";
        $this->strstitlex = "Title X";
        $this->strstitley = "Title Y";

        extract($setting);
        
        $this->xlfac = 1;
        $this->ylfac = 1;
        
        $this->im = @imagecreatetruecolor($this->imgw, $this->imgh) or die("Cannot Initialize new GD image stream");
        define('WHITE', imagecolorallocate($this->im, 255, 255, 255));
        $this->text_color = $this->hexColorAllocate($this->im, $this->text_rgb);
        $this->title_color = $this->hexColorAllocate($this->im, $this->title_rgb);
        $this->stitle_color = $this->hexColorAllocate($this->im, $this->stitle_rgb);
        $this->line_color = $this->hexColorAllocate($this->im, $this->line_rgb);
        $this->grid_color = $this->hexColorAllocate($this->im, $this->grid_rgb);
        $this->axis_color = $this->hexColorAllocate($this->im, $this->axis_rgb);
        $this->back_color = $this->hexColorAllocate($this->im, $this->back_rgb);
        $this->chart_color = $this->hexColorAllocate($this->im, $this->chart_rgb);
        $this->preparePlot(0,0,200,600);

    }
    function point($image, $x, $y, $c, $s = 10)
    {
        imagefilledellipse($image, $x, $y, $s, $s, $c);
        imageellipse($image, $x, $y, $s, $s, $c);
    }
    function righttext($image, $size, $ang, $xr, $yr, $c, $text)
    {
        $bsize = imagettfbbox($size, $ang, "/ttf/mm3.ttf", $text);

        $dx = ($bsize[2] - $bsize[0]);
        $dy = ($bsize[1] - $bsize[3]);
        $x = $xr - $dx;
        $y = $yr + $dy;
        imagettftext($image, $size, $ang, $x, $y, $c, "/ttf/mm3.ttf", $text);
    }
    function centertext($image, $size, $ang, $xr, $yr, $c, $text)
    {
        $bsize = imagettfbbox($size, $ang, "/ttf/mm3.ttf", $text);

        $dx = ($bsize[2] + $bsize[0]) / 2;
        $dy = ($bsize[1] + $bsize[3]) / 2;
        $x = $xr - $dx;
        $y = $yr - $dy;
        imagettftext($image, $size, $ang, $x, $y, $c, "/ttf/mm3.ttf", $text);
    }
    function digitCount($num)
    {
        if ($num == 0) return 1;
        return floor(log10(abs($num))) + 1;
    }
    
    function hexColorAllocate($im, $hex)
    {
        $hex = ltrim($hex, '#');
        $rgb = str_split($hex, 2);
        $r = hexdec($rgb[0]);
        $g = hexdec($rgb[1]);
        $b = hexdec($rgb[2]);
        return imagecolorallocate($im, $r, $g, $b);
    }
    
    function preparePlot($minx, $maxx, $miny, $maxy)
    {
    	$this->minx = $minx;
    	$this->maxx = $maxx;
    	$this->miny = $miny;
    	$this->maxy = $maxy;
    	
    	$xdigit = $this->digitCount($maxx);
        $ydigit = $this->digitCount($maxy);
        
        if ($xdigit > 5) $this->xlfac = 1000;
        else if ($xdigit > 4) $this->xlfac = 100;
        else if ($xdigit > 3) $this->xlfac = 10;
        if ($ydigit > 5) $this->ylfac = 1000;
        else if ($ydigit > 4) $this->ylfac = 100;
        else if ($ydigit > 3) $this->ylfac = 10;

        $this->x0 = $this->mgleft;
        $this->y0 = ($this->imgh - $this->mgbot);

        // $this->scx = ($this->imgw - $this->mgleft - $this->mgright) / ($maxx - $minx) / 1.05;
        // $this->scy = ($this->imgh - $this->mgtop - $this->mgbot - 2 * $this->tgrid) / ($maxy - $miny) / 1.05;
        $this->scx = ($maxx - $minx) != 0 ? ($this->imgw - $this->mgleft - $this->mgright) / ($maxx - $minx) / 1.05 : 0;
        $this->scy = ($maxy - $miny) != 0 ? ($this->imgh - $this->mgtop - $this->mgbot - 2 * $this->tgrid) / ($maxy - $miny) / 1.05 : 0;


        $roundgrid = $this->xlfac * 0.05;
        $this->sgx = (ceil(($maxx - $minx) / $this->xlfac / ($this->ngx) / $roundgrid)) * $this->xlfac * $roundgrid;
        $roundgrid = $this->ylfac * 0.05;
        $this->sgy = (ceil(($maxy - $miny) / $this->ylfac / ($this->ngy) / $roundgrid)) * $this->ylfac * $roundgrid;
        $this->xOff = 0;
        $this->yOff = 3 * $this->sgy;
        if ($minx < 0) $this->xOff = ceil((-$minx) / $this->sgx) * $this->sgx;
        if ($miny < 0) $this->yOff = - $miny + $this->tgrid / $this->scy;
    }
    function drawGrid()
    {
    	imagefill($this->im, 0, 0, $this->chart_color);
        imagefilledrectangle($this->im, $this->mgleft, $this->mgtop, $this->imgw - $this->mgright, $this->imgh - $this->mgbot, $this->back_color);

        imagesetthickness($this->im, 1);
        for ($i = 0;$i <= $this->ngx + 1;$i++)
        {
            $m = $i * $this->sgx * $this->scx;
            $x1 = $this->mgleft + $m;
            $tbuff = sprintf("%d", ($i * $this->sgx - $this->xOff) / $this->xlfac);
            if ($i <= $this->ngx)
            {
                imageline($this->im, $x1, $this->mgtop, $x1, $this->imgh - $this->mgbot, $this->grid_color);
                $this->righttext($this->im, $this->tgrid, 90, $x1, $this->y0 + 5, $this->text_color, $tbuff);
            }

            if ($i == $this->ngx + 1 && $this->xlfac != 1)
            {
                $tbuff = "×" . $this->xlfac;
                $tbuff = sprintf("x%.0E", $this->xlfac);
                $this->righttext($this->im, $this->tgrid * 3 / 4, 90, $x1, $this->y0 + 5, $this->stitle_color, $tbuff);
            }

        };
        $y = $this->miny - ($this->miny % $this->sgy);
        $y1 = - ($this->yOff + $y) * $this->scy + $this->y0;
        for ($i = 0;$i <= $this->ngy + 2 && $y < ($this->maxy + $this->sgy);$i++)
        {
            if ($i <= $this->ngy)
            {
                $tbuff = sprintf("%d", ($i * $this->sgy - $this->yOff) / $this->ylfac);
                $tbuff = sprintf("%d", ($y) / $this->ylfac);
                imageline($this->im, $this->x0, $y1, $this->imgw - $this->mgright, $y1, $this->grid_color);
                $this->righttext($this->im, $this->tgrid, 0, $this->x0 - 5, $y1, $this->text_color, $tbuff);
            }

            $y = $y + $this->sgy;
            $y1 = - ($this->yOff + $y) * $this->scy + $this->y0;
        }
        if ($this->ylfac != 1)
        {
            $tbuff = "×" . $this->ylfac;
            $tbuff = sprintf("x%.0E", $this->ylfac);
            $this->righttext($this->im, $this->tgrid * 3 / 4, 0, $this->x0 - 5, $y1, $this->stitle_color, $tbuff);
        }
        $y = $this->mgtop - $this->tgrid;
        if ($this->strstitle)
        {
            $this->centertext($this->im, $this->tstitle, 0, $this->imgw / 2, $y, $this->stitle_color, $this->strstitle);
            $y = $y - $this->ttitle;
        }
        $this->centertext($this->im, $this->ttitle, 0, $this->imgw / 2, $y, $this->title_color, $this->strtitle);

        $this->centertext($this->im, $this->tstitle, 90, $this->mgleft - 3 * $this->tgrid, $this->imgh / 2, $this->stitle_color, $this->strstitley);
        $this->centertext($this->im, $this->tstitle, 0, $this->imgw / 2, $this->imgh - $this->mgbot + $this->ttitle + 3 * $this->tgrid, $this->stitle_color, $this->strstitlex);

        imagesetthickness($this->im, 3);
        imageline($this->im, $this->x0, $this->y0 - $this->yOff * $this->scy, $this->imgw - $this->mgright, $this->y0 - $this->yOff * $this->scy, $this->axis_color);
        imageline($this->im, $this->x0 + $this->xOff * $this->scx, $this->mgtop, $this->x0 + $this->xOff * $this->scx, $this->imgh - $this->mgbot, $this->axis_color);
    }
    function plotGraph($data)
    {
    	$n = count($data[u]);
    	imagesetthickness($this->im, 2);
        $pointmode = $data[pointmode] ?? 0;
        $drawline = $data[drawline] ?? true;
        $drawvert = $data[drawvert] ?? false;
        $drawhorz = $data[drawhorz] ?? false;
        $showvalue = $data[showvalue] ?? false;
        $line_color = $this->hexColorAllocate($this->im, $data[color] ? : $this->line_rgb);
        $hline_color = $this->hexColorAllocate($this->im, $data[hcolor] ? : $this->line_rgb);
        $vline_color = $this->hexColorAllocate($this->im, $data[vcolor] ? : $this->line_rgb);
        $value_color = $this->hexColorAllocate($this->im, $data[value_color] ? : $this->text_rgb);
        $x1 = ($this->xOff + $data[u][0][0]) * $this->scx + $this->x0;
        $y1 = - ($this->yOff + $data[u][0][1]) * $this->scy + $this->y0;
        for ($i = 1;$i <= $n;$i++)
        {
            if ($i < $n)
            {
                $x2 = ($this->xOff + $data[u][$i][0]) * $this->scx + $this->x0;
                $y2 = - ($this->yOff + $data[u][$i][1]) * $this->scy + $this->y0;
                if ($drawline) imageline($this->im, $x1, $y1, $x2, $y2, $line_color);
            }
            if ($pointmode > 0) $this->point($this->im, $x1, $y1, $line_color, $this->psize);
            if ($drawvert) imageline($this->im, $x1, $y1, $x1, $this->y0 - $this->yOff * $this->scy, $vline_color);
            if ($drawhorz) imageline($this->im, $this->x0 + $this->xOff * $this->scx, $y1, $x1, $y1, $hline_color);
            if ($showvalue) {
            	$y = $y1 - 10;
            	if ($data[u][$i - 1][1] < 0)
            		$y = $y1 + $this->tgrid + 10;
            	$this->centertext($this->im, $this->tgrid, 0, $x1, $y, $value_color, "" . $data[u][$i - 1][1] / $this->ylfac);
            }
            $x1 = $x2;
            $y1 = $y2;
        }
    }
    function fitGraph($data)
    {
        $n = count($data[u]);
        //$minx = $maxx = $data[u][0][0];
        //$miny = $maxy = $data[u][0][1];
        
        $minx = $this->minx;
    	$maxx = $this->maxx;
    	$miny = $this->miny;
    	$maxy = $this->maxy;
    	
        for ($t = 0;$t < $n;$t++)
        {
            if ($data[u][$t][0] > $maxx) $maxx = $data[u][$t][0];
            else if ($data[u][$t][0] < $minx) $minx = $data[u][$t][0];

            if ($data[u][$t][1] > $maxy) $maxy = $data[u][$t][1];
            else if ($data[u][$t][1] < $miny) $miny = $data[u][$t][1];
        }
        $this->preparePlot($minx, $maxx, $miny, $maxy);
    }
    function viewImage()
    {
    	ob_start();
        imagepng($this->im);
        printf('<img src="data:image/png;base64,%s"/ width="$this->imgw">', base64_encode(ob_get_clean()));
    }
}

?>