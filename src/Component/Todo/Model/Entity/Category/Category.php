<?php

namespace Component\Todo\Model\Entity\Category;

/**
 * @Entity @Table(name="categories")
 **/
class Category
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    
    /** @Column(type="string") **/
    protected $title;
    
    /** @Column(type="text") **/
    protected $text;
}