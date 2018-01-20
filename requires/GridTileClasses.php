<?php
    class Tile
    {
        private $x;
        private $y;
        
        private $open = false;
        
        private $number;
        
        private $marked = false;
        
        public function show()
        {
            if(!$this->open)
            {
                if($this->marked)
                {
                    return '<td class="tile-marked" onmouseup="javascript:performMarkTileProcess(event,'.$this->x.','.$this->y.')"></td>';
                }
                else
                {
                    return '<td class="tile-closed" onmouseup="javascript:performOpenTileProcess(event,'.$this->x.','.$this->y.')"></td>';
                }
            }
            else
            {
                $res = "";
                switch($this->number)
                {
                    case -1: $res = '<td class="tile-mine"></td>'; break;
                    case 0: $res = '<td class="tile-open"></td>'; break;
                    default: $res = '<td class="tile-open tile-'.$this->number.'" ondblclick="javascript:performOpenNearProcess('.$this->x.','.$this->y.')">'.$this->number.'</td>';
                }
                
                return $res;
            }
        }
        
        public function __construct($x, $y) {
            $this->x = $x;
            $this->y = $y;
        }
        
        public function setNumber($number)
        {
            $this->number = $number;
        }
        
        
        public function getNumber()
        {
            return $this->number;
        }
        
        public function isOpen()
        {
            return $this->open;
        }
        
        public function Open()
        {
            $this->open = true;
        }
        
        public function Mark()
        {
            $this->marked = true;
        }
        
        public function Unmark()
        {
            $this->marked = false;
        }
        
        public function isMarked()
        {
            return $this->marked;
        }
    }
    
    class Grid
    {
        private $lost = false;
        
        public function Lost() { return $this->lost; }
        
        private $cols;
        private $rows;
        
        private $tiles;
        
        private $tilesToOpen;
        private $tilesOpened = 0;
        
        public function getTilesOpened()
        {
            return $this->tilesOpened;
        }
        
        private $tilesToMark;
        
        public function getTile($x, $y)
        {
            return $this->tiles[$x][$y];
        }
        
        public function getTilesToOpen() { return $this->tilesToOpen; }
        
        public function getCols() { return $this->cols; }
        public function getRows() { return $this->rows; }
        
        private $time = 0;
        
        public function getTime()
        {
            return $this->time;
        }
        
        public function setTime($newTime)
        {
            $this->time = $newTime;
        }
        
        private $timeTrial = '+';
        
        public function getTimeTrial()
        {
            return $this->timeTrial;
        }
        
        public function __construct($cols, $rows, $mines) {
            $this->cols = $cols;
            $this->rows = $rows;
            
            $this->CreateGrid();

            $this->CreateMines($mines);
            $this->SetTileNumbers();
            $this->tilesToOpen = ($this->cols * $this->rows) - $mines;
            $this->tilesToMark = $mines;
        }
        
        private function CreateGrid()
        {
            $this->tiles = array_fill(0, $this->cols, array());
            
            for($x=0; $x<$this->cols; $x++)
            {
                for($y=0; $y<$this->rows; $y++)
                {
                   $this->tiles[$x][$y] = new Tile($x, $y); 
                }
            }
        }
        
        private function CreateMines($mines)
        {
            $placed = 0;
            while($placed < $mines)
            {
                $rndX = rand(0, $this->cols-1);
                $rndY = rand(0, $this->rows-1);
                
                if($this->tiles[$rndX][$rndY]->getNumber() === null)
                { 
                    $this->tiles[$rndX][$rndY]->setNumber(-1);
                    $placed += 1;
                }
            }
        }
        
        private function SetTileNumbers()
        {
            for($x=0; $x<$this->cols; $x++)
            {
                for($y=0; $y<$this->rows; $y++)
                {
                    if($this->tiles[$x][$y]->getNumber() === null)
                    {
                        $this->tiles[$x][$y]->setNumber($this->GetTileTotalNumber($x, $y));     
                    }
                }
            }
        }
        
        private function GetTileTotalNumber($xPos, $yPos)
        {
            $total = 0;
            
            if($this->CheckSide(   $xPos-1,  $yPos    ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos-1,  $yPos-1  ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos,    $yPos-1  ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos+1,  $yPos-1  ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos+1,  $yPos    ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos+1,  $yPos+1  ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos,    $yPos+1  ) === -1) { $total += 1; }
            
            if($this->CheckSide(   $xPos-1, $yPos+1  ) === -1) { $total += 1; }
            
            return $total;
        }
        
        private function CheckSide($x, $y)
        {
            if($this->IsInGrid($x, $y))
            {
                $n = $this->tiles[$x][$y]->getNumber();
                if(isset($n))
                {
                    return $n;
                }
            }
            
            return 999;
        }
        
        public function MarkTile($x, $y)
        {
            if(!$this->tiles[$x][$y]->isOpen())
            {
                if(!$this->tiles[$x][$y]->isMarked())
                {
                    if($this->tilesToMark > 0)
                    {
                        $this->tiles[$x][$y]->Mark();
                        $this->tilesToMark -= 1;
                    }
                }
                else
                {
                    $this->tiles[$x][$y]->Unmark();
                    $this->tilesToMark += 1;
                }
            }    
        }
        
        public function CheckTile($x, $y)
        {
            if(!$this->tiles[$x][$y]->isOpen())
            {
                if(!$this->tiles[$x][$y]->isMarked())
                {
                    if($this->tiles[$x][$y]->getNumber() === -1)
                    {
                        $this->lost = true;
                        $this->tiles[$x][$y]->Open();
                    }
                    else
                    {
                        if($this->tiles[$x][$y]->getNumber() === 0)
                        {
                            $this->OpenNearTiles($x, $y);
                        }
                        else
                        {
                            $this->OpenTile($x, $y);
                        }
                    }                
                }
            }
        }
        
        private function OpenTile($x, $y)
        {
            $this->tiles[$x][$y]->Open();
            $this->tilesOpened += 1;
        }
        
        private function OpenNearTiles($xPos, $yPos)
        {
            $this->OpenTile($xPos, $yPos);
            
            $this->CheckOpenNearTile(   $xPos-1,  $yPos    );
            
            $this->CheckOpenNearTile(   $xPos-1,  $yPos-1  );

            $this->CheckOpenNearTile(   $xPos,    $yPos-1  );

            $this->CheckOpenNearTile(   $xPos+1,  $yPos-1  );

            $this->CheckOpenNearTile(   $xPos+1,  $yPos    );

            $this->CheckOpenNearTile(   $xPos+1,  $yPos+1  );

            $this->CheckOpenNearTile(   $xPos,    $yPos+1  );

            $this->CheckOpenNearTile(   $xPos-1,  $yPos+1  );
        }
        
        private function CheckOpenNearTile($x, $y)
        {
            if($this->IsInGrid($x, $y))
            {
                if(!$this->tiles[$x][$y]->isOpen())
                {
                    if(!$this->tiles[$x][$y]->isMarked())
                    {
                        if($this->tiles[$x][$y]->getNumber() === 0)
                        {
                            $this->OpenNearTiles($x, $y);
                        }
                        else
                        {
                            $this->OpenTile($x, $y);
                        } 
                    }
                }   
            }
        }
        
        function OpenDoubleClickTiles($xPos, $yPos)
        {
            $this->CheckDoubleClickNearTile(   $xPos-1,  $yPos    );
            
            $this->CheckDoubleClickNearTile(   $xPos-1,  $yPos-1  );
            
            $this->CheckDoubleClickNearTile(   $xPos,    $yPos-1  );
            
            $this->CheckDoubleClickNearTile(   $xPos+1,  $yPos-1  );
            
            $this->CheckDoubleClickNearTile(   $xPos+1,  $yPos    );
            
            $this->CheckDoubleClickNearTile(   $xPos+1,  $yPos+1  );
            
            $this->CheckDoubleClickNearTile(   $xPos,    $yPos+1  );
            
            $this->CheckDoubleClickNearTile(   $xPos-1,  $yPos+1  );
            
        }
        
        function CheckDoubleClickNearTile($x, $y)
        {
            if($this->lost) { return; }
            
            if($this->IsInGrid($x, $y))
            {
               if(!$this->tiles[$x][$y]->isOpen())
                {
                    if(!$this->tiles[$x][$y]->isMarked())
                    {
                        $num = $this->tiles[$x][$y]->getNumber();
                        if($num === -1)
                        {
                            $this->lost = true;
                            $this->OpenTile($x, $y);
                        }
                        else
                        {
                            if($num === 0)
                            {
                                $this->OpenNearTiles($x, $y);
                            }
                            else
                            {
                                $this->OpenTile($x, $y);
                            }
                        }
                    }
                } 
            }
        }
        
        private function IsInGrid($x, $y)
        {
            return (($x >= 0 && $x < $this->cols) && ($y >= 0 && $y < $this->rows));
        }
        
        public function CheckWin()
        {
            return ($this->tilesOpened === $this->tilesToOpen);
        }
        
        public function Show()
        {
            $result = '<table><tr><th style="text-align:left;padding-left:15px;" colspan="3">Mines: '.$this->tilesToMark.'</th><th colspan="'.($this->cols-7).'"></th><th style="text-align:right;padding-right:15px" colspan="4" id="timer">Time: 00:00:00</th></tr>';
            for($y=0; $y<$this->rows; $y++)
            {
                $result .= '<tr>';
                for($x=0; $x<$this->cols; $x++)
                {
                    $result .= $this->tiles[$x][$y]->show();
                }
                
                $result .= '</tr>';
            }
            
            $result .= '</table>';
            
            return $result;
        }
    }
?>