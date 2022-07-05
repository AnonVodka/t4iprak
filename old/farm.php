<?php

    require("animal.php");

    class Farm {
        private string $name;
        private array $animals;

        public function __construct(string $name)
        {
            $this->SetName($name);
            $this->animals = array();
        }
        
        public function SetName(string $name) {
            $this->name = $name;
        }

        public function GetName() : string {
            return $this->name;
        }

        public function AddAnimal($name, $type, $color = "None") {
            // $size = count($this->GetAnimals());
            // $this->animals[$size] = new Animal($name, $type, $color);
            $this->animals[] = new Animal($name, $type, $color);
        }

        public function GetAnimals() : array {
            return $this->animals;
        }

        public function GetAnimal(int $index) : Animal {
            $size = count($this->GetAnimals());
            if ($index > $size) {
                return "Invalid index!";
            }
            return $this->animals[$index];
        }

        public function __toString() : string
        {
            $size = count($this->GetAnimals());
            $str = "Welcome to our farm: '{$this->GetName()}'! <br/>";
            $str .= "Our farm has {$size} animal(s): <br/>";

            for ($i = 0; $i < $size; $i++) 
                $str .= $i+1 . ": " . $this->GetAnimal($i) . "<br/>";

            return $str;
        }

    }

?>