<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

class Serie extends Model implements Resource{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'series';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
		
		$schema -> string('name');

		$schema -> string('genres');

		$schema -> text('overview');

		$schema -> string('status');

		$schema -> file('poster');

		$schema -> file('banner');

		$schema -> toOne(ResourceContainer::class,'container');

		$schema -> toMany(Season::class,'seasons','serie_id');

		$schema -> toMany(Episode::class,'episodes','serie_id');

		$schema -> datetime('updated_at');

	}

	/**
	 * Return a complete array of this model (usefull in api)
	 *
	 * @return array
	 */
	public function toArrayComplete(){

		$res = parent::toArray();

		$res['poster'] = $this -> poster() -> getFullPath();
		$res['banner'] = $this -> banner() -> getFullPath();

		foreach(Episode::where('serie_id',$this -> id) -> get() as $episode){
			$episodes[] = $episode -> toArray();
		}
		

		return array_merge($res,['episodes' => $episodes,'container' => $this -> container -> toArray()]);
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
		$this -> overview = $response -> overview;
		$this -> status = $response -> status;

		if($response -> poster)
			$this -> poster() -> setByUrl($response -> poster);

		if($response -> banner)
			$this -> banner() -> setByUrl($response -> banner);

		$this -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 

		$this -> save();


		foreach($response -> episodes as $r_episode){

			$season = Season::firstOrCreate([
				'number' => $r_episode -> season,
				'serie_id' => $this -> id
			]);


			$episode = Episode::firstOrCreate([
				'number' => $r_episode -> number,
				'season_n' => $r_episode -> season,
				'season_id' => $season -> id,
				'serie_id' => $this -> id
			]);

			$episode -> name = $r_episode -> name;
			$episode -> overview = $r_episode -> overview;
			$episode -> aired_at = $r_episode -> aired_at;
			$episode -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s');
			$episode -> save();

		}
	}
}

?>