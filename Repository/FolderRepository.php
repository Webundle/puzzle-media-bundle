<?php

namespace Puzzle\MediaBundle\Repository;

use Puzzle\AdminBundle\Repository\PuzzleRepository;

/**
 * FolderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FolderRepository extends PuzzleRepository
{
    public function findByName($name, $parent = null) {
        $params = ['name' => '%'.$name.'%'];
        $builder = $this->createQueryBuilder('f')
                        ->where('f.name LIKE :name');
        
        if (false === empty($parent)) {
            $builder->andWhere('f.parent = :parent');
            $params['parent'] = $parent;
        }else {
            $builder->andWhere('f.parent IS NOT NULL');
        }
        
        return $builder->orderBy('f.createdAt', 'DESC')
                       ->setParameters($params)
                       ->getQuery()
                       ->getResult();
        
    }
    
    public function findByAppName($appName, $parent = null) {
        $params = ['appName' => $appName];
        $builder = $this->createQueryBuilder('f')
                    ->where('f.appName = :appName');
        
        if (false === empty($parent)) {
            $builder->andWhere('f.parent = :parent');
            $params['parent'] = $parent;
        }else {
            $builder->andWhere('f.parent IS NOT NULL');
        }
            
        return $builder->orderBy('f.createdAt', 'DESC')
                       ->setParameters($params)
                       ->getQuery()
                       ->getResult();
                    
    }
    
    public function findByReverseAppName($appName, $parent = null) {
        $params = ['appName' => $appName];
        $builder = $this->createQueryBuilder('f')
                        ->where('f.appName != :appName');
        
        if (false === empty($parent)) {
            $builder->andWhere('f.parent = :parent');
            $params['parent'] = $parent;
        }else {
            $builder->andWhere('f.parent IS NOT NULL');
        }
        
        return $builder->orderBy('f.createdAt', 'DESC')
                       ->setParameters($params)
                       ->getQuery()
                       ->getResult();
        
    }
}
