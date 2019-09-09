<?php

namespace App\Controller;

use App\Entity\Task;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
	public function index()
	{
		$repository = $this->getDoctrine()->getRepository(Task::class);
		$tasks = $repository->findAll();

		return $this->render('taskmanager/list.html.twig', 
		['tasks' => $tasks]);
	}

	public function list()
	{
		$repository = $this->getDoctrine()->getRepository(Task::class);
		$tasks = $repository->findAll();
		$status = 200;
		$response = [];

		foreach($tasks as $task) {
			$date = $task->getCreated();
			array_push($response,[
				'title' => $task->getTitle(),
				'created' => $date->format('c'),
				'updated' => $task->getUpdated()->format('c'),
				'description' => $task->getDescription(),
				'status' => $task->getStatus(),
				'id' => $task->getId()
			]);
		}

		return $this->json($response,$status);
	}

	public function create()
	{
		$status = 200;
		$post = file_get_contents('php://input');
		$post = json_decode($post);
		$entityManager = $this->getDoctrine()->getManager();
		$error = false;

		$task = new Task();
		$task->setCreated(date_create('now'));
		$task->setTitle($post->title);
		$task->setStatus($post->status);
		$task->setDescription($post->description);
		$task->setUpdated(date_create('now'));

		$entityManager->persist($task);

		if($error) {
			$status = 500;
			$res = 'Something went wrong.';
		}
		
		$entityManager->flush();
		$res = 'Saved task with id of ' . $task->getId();

		return $this->json($res,$status);
	}

	public function view($id)
	{
		$repository = $this->getDoctrine()->getRepository(Task::class);
		$task = $repository->find($id);

		$status = 200;
		$data = [
			'title' => $task->getTitle(),
			'created' => $task->getCreated()->format('c'),
			'updated' => $task->getUpdated()->format('c'),
			'description' => $task->getDescription(),
			'status' => $task->getStatus(),
			'id' => $task->getId()
		];

		return $this->json($data, $status);
	}

	public function update($id)
	{
		$post = file_get_contents('php://input');
		$post = json_decode($post);
		$entityManager = $this->getDoctrine()->getManager();
		$task = $entityManager->getRepository(Task::class)->find($id);
		$status = 200;

		if (!$task) {
			$status = 410;
			return $this->json('Task does not exist', $status);

		}

		$task->setTitle($post->title);
		$task->setDescription($post->description);
		$task->setUpdated(date_create('now'));
		$task->setStatus($post->status);

		$entityManager->flush();
		$data = 'Updated task '. $id;

		return $this->json($data, $status);
	}

	public function delete($id)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(Task::class);
		$task = $repository->find($id);
		$status = 200;
		$data = 'Deleted task '. $id;

		$entityManager->remove($task);
		$entityManager->flush();


		return $this->json($data, $status);
	}
}
