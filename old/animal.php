<?php

    interface IAnimal {
        public function __construct(string $name, string $type, string $color);

        public function GetName() : string;
        public function SetName(string $name);

        public function GetType() : string;
        public function SetType(string $type);

        public function GetColor() : string;
        public function SetColor(string $color);

        public function __toString() : string;
    }

    class Animal implements IAnimal {
        private string $name;
        private string $type = "Animal";
        private string $color;

        public function __construct(string $name = "None", string $type = "Animal", string $color = "black")
        {
            $this->SetName($name);
            $this->SetType($type);
            $this->SetColor($color);
        }

        public function SetName(string $name) {
            $this->name = $name;
        }

        public function GetName() : string {
            return $this->name;
        }

        public function SetType(string $type) {
            $this->type = $type;
        } 

        public function GetType() : string {
            return $this->type;
        }

        public function SetColor(string $color) {
            $this->color = $color;
        }

        public function GetColor() : string {
            return $this->color;
        }

        public function __toString() : string
        {
            return "This animal is named {$this->GetName()} and is a(n) {$this->GetType()}. His color is {$this->GetColor()}.";
        }
    }   
?>