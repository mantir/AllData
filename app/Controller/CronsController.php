<?php //ini_set('max_execution_time', 180); Funktioniert auf Server nicht, da PHP wahrscheinlich in Safe Mode
App::uses('EventsController', 'Controller');
/**
 * Crons Controller
 *
 * @property Cron $Cron
 */
class CronsController extends EventsController {

/**
 * index method
 *
 * @return void
 */
	public function index($id, $start = 0, $type = 'all') { 
		$allowID = 'dsjbfsdbhib3b432b514z4332';
		if($type == 'all')
			//$type = 'fetch,hbbtv,mediathek';
			$type = 'fetch,hbbtv';
		if($id != $allowID) return;
		$this->loadModel('Station');
		$this->loadModel('Event');
		$this->loadModel('Setting');
		$settings = $this->Setting->get(array(
			'webLastDays' => 'import.mediathek.web.lastDays', 
			'hbbtvLastDays' => 'import.mediathek.hbbtv.lastDays', 
			'epgLastDays' =>'import.program.epg.lastDays', 
			'epgNextWeeks' => 'import.program.epg.nextWeeks')
		);
		extract($settings);
		$stations = $this->Station->find('all', array('recursive' => 0, 'order' => 'Station.rank ASC'));
		//$pre = 3; $next = 7; //-1: Importiere die letzten 3 und die nächsten 7 Tage
		$weeks = $epgNextWeeks; //Importiere die nächsten x Wochen
		$date = strtotime(date('d.m.Y').' -'.$epgLastDays.' days'); //Starte x Tage in der Vergangenheit
		$webDate = strtotime(date('d.m.Y').' -'.$webLastDays.' days'); //Starte x Tage in der Vergangenheit
		$hbbtvDate = strtotime(date('d.m.Y').' -'.$hbbtvLastDays.' days'); //Starte x Tage in der Vergangenheit
		
		$eventDays = $weeks * 7 + $epgLastDays;
		$startTime = time();
		$c = 0;
		foreach($stations as $j => $st){
			if($j < $start) continue;
			if($c > 1) break;
			$c++;
			if(strpos($type, 'fetch') !== false) 
			for($i = 0; $i <= $eventDays; $i++){ //Aus EPG importieren
				$day = strtotime('+'.$i.' days', $date);
				debug('Fetch '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetch($st['Station']['id'], date('d.m.Y', $day));
			}
			if(strpos($type, 'mediathek') !== false)
			for($i = 0; $i <= $webLastDays; $i++){ //Aus Web-Mediathek importieren
				$day = strtotime('+'.$i.' days', $date);
				debug('Fetch Mediathek '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetchMediathek($st['Station']['mapping'], date('d.m.Y', $day));
			}
			if(strpos($type, 'hbbtv') !== false)
			for($i = 0; $i <= $hbbtvLastDays; $i++){ //Aus HbbTV-Mediathek importieren
				$day = strtotime('+'.$i.' days', $date);
				debug('Fetch HbbTV '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetchHbbtv($st['Station']['mapping'], date('d.m.Y', $day));
			}
		}
		debug(date('i:s', time() - $startTime).' Min.');
		$this->layout = 'template';
		$this->set(compact('Crons', 'stations'));
		$this->render('../dummy');
	}
	
	public function station($station, $id, $days = 2, $type = 'all') {
		$allowID = 'dsjbfsdbhib3b432b514z4332';
		if($type == 'all')
			//$type = 'fetch,hbbtv,mediathek';
			$type = 'fetch,hbbtv';
		if($id != $allowID) return;
		$this->loadModel('Station');
		$this->loadModel('Event');
		$stations = $this->Station->find('all', array('conditions' => 'mapping="'.$station.'"', 'recursive' => 0, 'order' => 'Station.name'));
		$date = strtotime(date('d.m.Y'));
		$c = 0;
		foreach($stations as $j => $st){
			if($c > 1) break;
			$c++;
			if(strpos($type, 'fetch') !== false)
			for($i = 0; $i <= $days; $i++){
				$day = strtotime('-'.$i.' days', $date);
				debug('Fetch '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetch($st['Station']['id'], date('d.m.Y', $day));
			}
			if(strpos($type, 'mediathek') !== false)
			for($i = 0; $i <= $days; $i++){
				$day = strtotime('-'.$i.' days', $date);
				debug('Fetch Mediathek '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetchMediathek($st['Station']['mapping'], date('d.m.Y', $day));
			}
			if(strpos($type, 'hbbtv') !== false)
			for($i = 0; $i <= $days; $i++){
				$day = strtotime('-'.$i.' days', $date);
				debug('Fetch HbbTV '.$st['Station']['name'].' '.date('d.m.Y', $day));
				$this->fetchHbbtv($st['Station']['mapping'], date('d.m.Y', $day));
			}
		}
		$this->layout = 'template';
		$this->set(compact('Crons', 'stations'));
		$this->render('../dummy');
	}	
	
	function bilder_import(){
		$this->loadModel('Event');
		if(intval(date('H')) == 0)
			$this->fetchImages();
		$this->fetchImages('ardfoto');
		$this->render('../dummy');
	}
	
	function cleanUp($id){
		$allowID = 'dsjbfsdbhib3b432b514z4332';
		if($id != $allowID) return;
		$deleteTime = strtotime('-14 days');
		$this->loadModel('Event');
		$cond = ' WHERE startTime < '.$deleteTime;
		$this->Event->Video->query('DELETE FROM videos WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->Video->query('DELETE FROM videos_videos WHERE video_id NOT IN (SELECT id FROM videos) OR similar_id NOT IN (SELECT id FROM videos)');
		$this->Event->Text->query('DELETE FROM texts WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->Link->query('DELETE FROM links WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->Attribute->query('DELETE FROM attributes WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->Image->query('DELETE FROM images WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->query('DELETE FROM events_tags WHERE event_id IN (SELECT id FROM events'.$cond.')');
		$this->Event->query('DELETE FROM events'.$cond);
		$this->Event->query('DELETE FROM logs WHERE time < '.$deleteTime);
		$this->Event->Image->query('DELETE FROM images WHERE (time < '.time().' AND event_id = 0)'); //Alle Bilder löschen, die nie gematcht wurden
		$this->render('../dummy');
	}
	
	function realCleanUp(){
		$this->loadModel('Event');
		$this->Event->query('DELETE FROM events_tags WHERE event_id NOT IN (SELECT id FROM events)');
		$this->Event->query('DELETE FROM texts WHERE event_id NOT IN (SELECT id FROM events)');
		$this->Event->query('DELETE FROM images WHERE event_id NOT IN (SELECT id FROM events)');
	}
	

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Cron->id = $id;
		if (!$this->Cron->exists()) {
			throw new NotFoundException(__('Invalid Cron'));
		}
		$this->set('Cron', $this->Cron->read(null, $id));
		//$this->er(console::bug($this->Cron->read(null, $id)));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Cron->create();
			if ($this->Cron->save($this->request->data)) {
				$this->Session->setFlash(__('The Cron has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Cron could not be saved. Please, try again.'));
			}
		}
		$stations = $this->Cron->Station->find('list');
		$attributes = $this->Cron->Attribute->find('list');
		$this->set(compact('stations', 'attributes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Cron->id = $id;
		if (!$this->Cron->exists()) {
			throw new NotFoundException(__('Invalid Cron'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Cron->save($this->request->data)) {
				$this->Session->setFlash(__('The Cron has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Cron could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Cron->read(null, $id);
		}
		$stations = $this->Cron->Station->find('list');
		$attributes = $this->Cron->Attribute->find('list');
		$this->set(compact('stations', 'attributes'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Cron->id = $id;
		if (!$this->Cron->exists()) {
			throw new NotFoundException(__('Invalid Cron'));
		}
		if ($this->Cron->delete()) {
			$this->Session->setFlash(__('Cron deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Cron was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
