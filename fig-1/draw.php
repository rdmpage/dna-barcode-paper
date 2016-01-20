<?php

//-------------------------------------------------------------------------------------------------
class Port
{
	var $output = '';
	var $width = 0;
	var $height = 0;
	var $element_id = 0;
	
	//----------------------------------------------------------------------------------------------
	function __construct($element_id, $width, $height)
	{
		$this->element_id 	= $element_id;
		$this->width 		= $width;
		$this->height 		= $height;
		$this->StartPicture();
	}
	
	//----------------------------------------------------------------------------------------------
	function DrawLine($p0, $p1)
	{
	}
	
	//----------------------------------------------------------------------------------------------
	function DrawRect($p0, $p1)
	{
	}
	
	
	function DrawPolygon($pts, $color = array())
	{
	}
	
	//----------------------------------------------------------------------------------------------
	function DrawText ($pt, $text)
	{
	}
	
	//----------------------------------------------------------------------------------------------
	function GetOutput()
	{
		$this->EndPicture();
		return $this->output;
	}
	
	//----------------------------------------------------------------------------------------------
	function StartPicture ()
	{
	}

	//----------------------------------------------------------------------------------------------
	function EndPicture ()
	{
	}
	
	//----------------------------------------------------------------------------------------------
	function StartGroup($transform = "")
	{
	}	
	
	//----------------------------------------------------------------------------------------------
	function EndGroup()
	{
	}	
	
	
	
}

//-------------------------------------------------------------------------------------------------
class CanvasPort extends Port
{
	
	
	function DrawLine($p0, $p1)
	{
		$this->output .= 'context.moveTo(' . $p0['x'] . ',' . $p0['y'] . ');' . "\n";
		$this->output .= 'context.lineTo(' . $p1['x'] . ',' . $p1['y'] . ');' . "\n";
		$this->output .= 'context.stroke();' . "\n";
	}
	
	function DrawText ($pt, $text)
	{
		$this->output .= 'context.fillText("' . $text . '", ' . $pt['x'] . ', ' . $pt['y'] . ');' . "\n";
	}
	
	function StartPicture ()
	{
		$this->output = '<script type="application/javascript">' . "\n";
		$this->output .= 'var paper = Raphael("' . $this->element_id . '", ' . $this->width . ', ' . $this->height . ');' . "\n";
	}
		
	
	function EndPicture ()
	{
		$this->output .= '</script>';
	}
	
	
}

//-------------------------------------------------------------------------------------------------
class SVGPort extends Port
{
		
	function DrawLine($p0, $p1)
	{
/*		$this->output .= '<path d="M ' 
				. $p0['x'] . ' ' . $p0['y'] . ' ' . $p1['x'] . ' ' . $p1['y'] . '" />';
*/
		$this->output .= '<line  x1="' 
				. $p0['x'] . '" y1="' . $p0['y'] . '" x2="' . $p1['x'] . '" y2="' . $p1['y'] . '"'
				
				. ' vector-effect="non-scaling-stroke" '
				. ' stroke-width="0.5"'
				
				. ' />';
				
		/*		
  <line
     style="stroke:#000000;stroke-width:1;stroke-linecap:square"
     id="line9"
     y2="10"
     x2="19.743589"
     y1="10"
     x1="390"
     vector-effect="non-scaling-stroke" />
	*/			
				
	}
	
	//----------------------------------------------------------------------------------------------
	function DrawRect($p0, $p1, $color = array())
	{
		$this->output .= '<rect';
		
		if (count($color) > 0)
		{
			$this->output .= ' fill="rgb(' . join(",", $color) . ')"';
		}
		else
		{
			$this->output .= ' fill="#ff0000"';
		}
		//$this->output .= ' style="opacity:0.3;"'; 
		$this->output .= ' stroke="#000000"'; 
		$this->output .= ' stroke-width="0.5"'; 
		$this->output .= ' vector-effect="non-scaling-stroke"';

		$this->output .= ' x="' . $p0['x'] . '"';	
		$this->output .= ' y="' . $p0['y'] . '"';	
		$this->output .= ' width="' . ($p1['x'] - $p0['x']) . '"';	
		$this->output .= ' height="' . ($p1['y'] - $p0['y']) . '"';	
		$this->output .= ' />';
		
	}
	
	//------------------------------------------------------------------------------------
	function DrawPolygon($pts, $color = array())
	{
		$this->output .= '<polygon';
		
		if (count($color) > 0)
		{
			$this->output .= ' fill="rgb(' . join(",", $color) . ')"';
		}
		else
		{
			$this->output .= ' fill="#ff0000"';
		}
		
		$this->output .= ' stroke="#333333" points="';
		
		foreach ($pts as $pt)
		{
			$this->output .=  $pt['x'] . ',' . $pt['y'] . ' ';
		}
		$this->output .= '" />';
	}
	
	//------------------------------------------------------------------------------------
	function DrawText ($pt, $text, $fontsize = 20, $align = 'left')
	{
	
		//	$this->output .= '<text style="fill:none;fill-opacity:1;stroke:#FFFFFF;stroke-width:4px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;alignment-baseline:middle;font-size:' . $fontsize . 'px;"';
		// $this->output .= ' x="' . $pt['x'] . '" y="' . $pt['y'] . '">' . $text . '</text>' .  "\n";
	
	
		
		$this->output .= '<text style="color:#ffffff;font-size:' . $fontsize . 'px;"';
		$this->output .= ' x="' . $pt['x'] . '" y="' . $pt['y'] . '">' . $text . '</text>' .  "\n";
		
		
/*
	switch (align)
	{
		case 'left':
			text.setAttribute('text-anchor', 'start');
			break;
		case 'centre':
		case 'center':
			text.setAttribute('text-anchor', 'middle');
			break;
		case 'right':
			text.setAttribute('text-anchor', 'end');
			break;
		default:
			text.setAttribute('text-anchor', 'start');
			break;
	}
*/		
	}
	
	//------------------------------------------------------------------------------------
	function StartPicture()
	{
		$this->output = '<?xml version="1.0" ?>
<svg xmlns:xlink="http://www.w3.org/1999/xlink" 
	xmlns="http://www.w3.org/2000/svg"
	width="' . $this->width . 'px" 
    height="'. $this->height . 'px" 
    >';	
    
    	$this->output .= '<style type="text/css">
<![CDATA[

text {
	/*font-size: 10px;*/
	color: black;
}

path {
	stroke:#000000;
	stroke-width:1;
	/*stroke-linecap:square;*/
}

line {
	stroke: #000000;
	stroke-width:1;
	stroke-linecap:square;
}

]]>
</style>';

    
    }

	//------------------------------------------------------------------------------------
	function EndPicture ()
	{		
		$this->output .= '</svg>';
	}
	
	
	//------------------------------------------------------------------------------------
	function StartGroup($transform = '')
	{
		$this->output .= '<g id="' . $this->element_id . '"';
		if ($transform != '')
		{
			$this->output .= ' transform="' . $transform . '"';
		}
		$this->output .= '>';	
	}
	
	function EndGroup()
	{
		$this->output .= '</g>';
	}
	
}



$text = file_get_contents('data.txt');

$lines = explode("\n", $text);

//print_r($lines);

$count = 0;

$x_axis = array();

foreach ($lines as $line)
{
	$parts = explode("\t", $line);
	
	if ($count == 0)
	{
		$headings = $parts;
	}
	else
	{
		$x_axis[] = $parts[0];
		
		for ($i = 1; $i < count($headings); $i++)
		{
			if ($parts[$i] != "")
			{
				$data[$headings[$i]][$parts[0]] = $parts[$i];
			}
		}
	}
	$count++;
}

//print_r($data);
//print_r($headings);
//print_r($x_axis);

//exit();



$height = 100;
$width = 500;

$gap = $width/(count($x_axis) - 1);

$x_offset = 100;

$port = new SVGPort("g", 1000, 1000);

$port->StartPicture();

for ($j = 1; $j < count($headings); $j++)
{
	$origin_y = $height * ($j - 1);	
	
	$port->DrawRect(
		array('x' => 0, 'y' => $origin_y),
		array('x' => $width, 'y' => $origin_y + $height),
		array(255,255,255)
		);
	
	// maximum value

	$max = 0;
	foreach ($data[$headings[$j]] as $k => $v)
	{
		$max = max($max, $v);
	}

	$max_x = $x_axis[count($x_axis) - 1];
	$min_x = $x_axis[0];

	$pts = array();

	$pts[] = array('x' => 0, 'y' => $origin_y);

/*
	for ($i = 0; $i < count($data[$j]); $i++)
	{
		$pts[] = array('x' => ($i * $gap), 'y' => ($height - $data[$j][$i]/$max*$height) + $origin_y);

	}
*/

	//print_r($data[$headings[$j]]);
	
	foreach ($data[$headings[$j]] as $k => $v)
	{
		$pts[] = array('x' => (($k - $x_axis[0]) * $gap), 'y' => ($height - $v/$max*$height) + $origin_y);
	
	}

	$pts[] = array('x' => (count($x_axis) - 1) * $gap, 'y' => $origin_y + $height);
	$pts[] = array('x' => 0, 'y' => $origin_y + $height);
	
	$pts[] = array('x' => 0, 'y' => $origin_y);

	$port->DrawPolygon($pts, array(228,228,228));
	
	// 	function DrawText ($pt, $text, $fontsize = 0.1, $align = 'left')

	$fontsize = 20;
	$port->DrawText( array('x' => count($x_axis) * $gap + $fontsize/2, 'y' => $origin_y + $fontsize), $headings[$j], $fontsize);
	
}


// time scale
$ticks = array(1864, 1900, 1950, 2000, 1923, 2015);

foreach ($ticks as $tick)
{
	$port->DrawLine(
		array('x' => (($tick - $x_axis[0]) * $gap), 'y' => 0),
		array('x' => (($tick - $x_axis[0]) * $gap), 'y' => (count($headings) - 1) * $height	)
		);
		
	$fontsize = 20;
	$port->DrawText(array('x' => (($tick - $x_axis[0]) * $gap), 'y' => (count($headings) - 1) * $height + $fontsize), 
		$tick, $fontsize);
	
	
}



echo $port->GetOutput();


?>
