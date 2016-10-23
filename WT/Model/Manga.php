<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

class Manga extends Model implements Resource{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'manga';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
		
		$schema -> string('name');

		$schema -> string('type');

		$schema -> string('genres');

		$schema -> text('overview');

		$schema -> text('status');

		$schema -> file('poster');

		$schema -> file('banner');

		$schema -> toOne(ResourceContainer::class,'container');

		$schema -> toMany('volumes',Volume::class,'manga');

		$schema -> toMany('chapters',Chapter::class,'manga');
		
		$schema -> updated_at();

	}

	/**
	 * Return a complete array of this model (usefull in api)
	 *
	 * @return array
	 */
	public function toArrayComplete(){

		$res = parent::toArray();

		$res['poster'] = $this -> poster() -> getFullPath();
		$res['banner'] = $res['poster'];

		foreach(Chapter::where('manga_id',$this -> id) -> get() as $chapter){
			$chapters[] = $chapter -> toArray();
		}
		

		return array_merge($res,['chapters' => $chapters,'container' => $this -> container -> toArray()]);
	}

	/**
	 * Fill this entity using a generic response from database api
	 *
	 * @param object $response
	 * @param Container $container
	 */
	public function fillFromDatabaseApi($response,$container){

		$this -> container = $container;

		$this -> name = $response -> name;
		$this -> type = $response -> type;
		$this -> overview = $response -> overview;
		$this -> status = $response -> status;

		if($response -> poster)
			$this -> poster() -> setByUrl($response -> poster);

		if($response -> banner)
			$this -> banner() -> setByUrl($response -> banner);

		$this -> save();


		foreach($response -> chapters as $r_chapter){

			
			$volume = Volume::firstOrCreate([
				'number' => $r_chapter -> season,
				'manga_id' => $this -> id,
				'number' => $r_chapter -> volume,
			]);


			$chapter = Chapter::firstOrCreate([
				'number' => $r_chapter -> number,
				'manga_id' => $this -> id,
			]);
			
			$chapter -> volume_n = $r_chapter -> volume;
			$chapter -> volume = $volume;
			$chapter -> name = $r_chapter -> name;
			$chapter -> scan = $r_chapter -> scan;
			$chapter -> released_at = new \DateTime($r_chapter -> released_at);
			$chapter -> save();

		}
	}
}

?>