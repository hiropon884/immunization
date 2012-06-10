<?php
class medicine
{
  private $id;
  private $name;
  private $regular;
  private $kinds;
  private $frequency;
  private $comment;
  private $timestamp;
  private $head = array();
  private $tail = array();

  function setId($key){
    $this->id = $key;
  }
  function setName($key){
    $this->name = $key;
  }
  function setRegular($key){
    $this->regular = $key;
  }
  function setKinds($key){
    $this->kinds = $key;
  }
  function setFrequency($key){
    $this->frequency = $key;
  }
  function setComment($key){
    $this->comment = $key;
  }
  function setTimestamp($key){
    $this->timestamp = $key;
  }
  
  function pushTerm($start, $end){
    $this->head[] = $start;
    $this->tail[] = $end;
  }
  function getId(){
    return $this->id;
  }
  function getName(){
    return $this->name;
  }
  function getRegular(){
    return $this->regular;
  }
  function getKinds(){
    return $this->kinds;
  }
  function getFrequency(){
    return $this->frequency;
  }
  function getComment(){
    return $this->comment;
  }
  function getTimestamp(){
    return $this->timestamp;
  }
  
  function getHeadPeriod($time){
    return $this->head[$time];
  }
  function getTailPeriod($time){
    return $this->tail[$time];
  }
}

?>