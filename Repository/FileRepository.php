<?php

namespace Puzzle\MediaBundle\Repository;

use Puzzle\AdminBundle\Repository\PuzzleRepository;
use Puzzle\MediaBundle\Util\MediaUtil;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends PuzzleRepository
{
	/**
	 * Find by name
	 * @param string $name
	 */
	public function findByName(string $name = null)
	{
	   $builder = $this->createQueryBuilder('f')
	                   ->select()
                       ->distinct();
	   
        if ($name) {
           $builder->where('f.displayName LIKE :name')
                   ->setParameter(':name', '%'.$name.'%');
        }
        
        return $builder->orderBy('f.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
	}
	
	/**
	 * Find by ids
	 * 
	 * @param string $list
	 * @return array
	 */
	public function findByIds(array $ids)
	{
	    foreach ($ids as $key => $id){
	        if($key == 0){
	            $list = "'".$id."'";
	        }else{
	            $list .= ",'".$id."'";
	        }
	    }
	    
	    return $this->createQueryBuilder('f')
	                ->select()
	                ->distinct()
	                ->where('f.id IN ('.$list.')')
	                ->orderBy('f.createdAt', 'DESC')
	                ->getQuery()
	                ->getResult();
	}
	
	/**
	 * Find files which are pictures
	 *
	 * @param string $list
	 * @return array
	 */
	public function findByListIdInversed($list)
	{
		$array = explode(",", $list);
		foreach ($array as $key => $item){
			if($key == 0){
				$list = "'".$item."'";
			}else{
				$list .= ",'".$item."'";
			}
		}
		return $this->_em
					->createQuery("SELECT f FROM ". $this->_entityName . " f WHERE f.id NOT IN (".$list.")")
					->getResult();
	}
	
	/**
	 * Find by path
	 * mixed paths list or array of paths
	 */
	public function findByPaths($paths)
	{
		$paths = !is_array($paths) ? [$paths] : $paths;
		$listPaths = "";
		foreach ($paths as $key => $item){
			if($key == 0){
				$listPaths = "'".$item."'";
			}else{
				$listPaths .= ",'".$item."'";
			}
		}
		
		return $this->_em
		->createQuery("SELECT f FROM ". $this->_entityName . " f WHERE f.path IN (".$listPaths.")")
// 					->setParameter('listPaths',$listPaths)
					->getResult();
	}
	
	public function constructInClause($data, $delimiter = ',') {
	    $data = !is_array($data) ? explode($delimiter, $data) : $data;
	    $list = "";
	    
	    foreach ($data as $key => $item){
	        if($key == 0){
	            $list = "'".$item."'";
	        }else{
	            $list .= ",'".$item."'";
	        }
	    }
	    
	    return $list;
	}
}
