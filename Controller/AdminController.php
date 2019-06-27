<?php

namespace Puzzle\MediaBundle\Controller;

use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Form\Type\FolderCreateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Puzzle\MediaBundle\Event\FolderEvent;
use Puzzle\MediaBundle\Form\Type\FolderUpdateType;
use Puzzle\MediaBundle\Entity\Comment;
use Puzzle\MediaBundle\Form\Type\FileUpdateType;
use Puzzle\MediaBundle\Form\Type\VideoUpdateType;
use Puzzle\MediaBundle\Form\Type\AudioUpdateType;
use Puzzle\MediaBundle\Entity\Picture;
use Puzzle\MediaBundle\Entity\Audio;
use Puzzle\MediaBundle\Entity\Video;
use Puzzle\MediaBundle\Entity\Document;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AdminController extends Controller
{
	/***
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listFilesAction(Request $request){
	    $em = $this->getDoctrine()->getManager();
	    
	    switch ($request->query->get('type')) {
	        case "picture":
	            $filters = "*.(". MediaUtil::supportedPictureExtensions().")";
	            $items = $em->getRepository(Picture::class)->findByName($request->query->get('search'));
	            
	            break;
	        case "audio":
	            $filters = "*.(".MediaUtil::supportedAudioExtensions().")";
	            $items = $em->getRepository(Audio::class)->findByName($request->query->get('search'));
	            break;
	        case "video":
	            $filters = "*.(".MediaUtil::supportedVideoExtensions().")";
	            $items = $em->getRepository(Video::class)->findByName($request->query->get('search'));
	            break;
	        case "document":
	            $filters = "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $items = $em->getRepository(Document::class)->findByName($request->query->get('search'));
	            break;
	        default:
	            $filters = "*";
	            $items = $em->getRepository(File::class)->findByName($request->query->get('search'));
	            break;
	    }
	    
	    $folders = $em->getRepository(Folder::class)->findBy(['appName' => 'media'], ['createdAt' => 'DESC']);
	    
	    if ($request->get('target') == 'modal') {
	        return $this->render("MediaBundle:Admin:list_files_in_modal.html.twig", array(
	            'type' =>  $request->query->get('type'),
	            'filters' => $filters,
	            'items' => $items,
	            'enableMultipleSelect' => $request->get('multiple_select') ? true: false,
	            'context' => $request->get('context')
	        ));
	    }
	    
	    return $this->render("MediaBundle:Admin:list_files.html.twig",[
	        'items' => $items,
	        'type' => $request->query->get('type'),
	        'folders' => $folders,
	        'filters' => $filters
	    ]);
	}
	
	/***
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function browseFilesAction(Request $request){
	    $em = $this->getDoctrine()->getManager();
	    $type = $request->get('type');
	    $filters = null;
	    
	    switch ($request->query->get('type')) {
	        case "picture":
	            $filters = "*.(". MediaUtil::supportedPictureExtensions().")";
	            $items = $em->getRepository(Picture::class)->findByName($request->query->get('search'));
	            
	            break;
	        case "audio":
	            $filters = "*.(".MediaUtil::supportedAudioExtensions().")";
	            $items = $em->getRepository(Audio::class)->findByName($request->query->get('search'));
	            break;
	        case "video":
	            $filters = "*.(".MediaUtil::supportedVideoExtensions().")";
	            $items = $em->getRepository(Video::class)->findByName($request->query->get('search'));
	            break;
	        case "document":
	            $filters = "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $items = $em->getRepository(Document::class)->findByName($request->query->get('search'));
	            break;
	        default:
	            $filters = "*";
	            $items = $em->getRepository(File::class)->findByName($request->query->get('search'));
	            break;
	    }
	    
	    return $this->render("MediaBundle:Admin:browse_files.html.twig",[
	        'type' => $type,
	        'filters' => $filters,
	        'items' => $items,
	        'enableMultipleSelect' => $request->get('multiple_select') ? true: false,
	        'context' => $request->get('context')
	    ]);
	}
	
	/**
	 * Embed file
	 * 
	 * @param Request $request
	 * @param mixed $formData
	 * @param mixed $data
	 * @param mixed $type
	 * @param mixed $context
	 * @param boolean $multiple
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function embedFileAction(Request $request, $formData, $data, $type, $context, $multiple = false) {
	    $filters = '*';
	    $accept = '*';
	    
	    switch ($type){
	        case "picture":
	            $filters = "*.(". MediaUtil::supportedPictureExtensions().")";
	            $accept = 'image/*';
	            break;
	        case "audio":
	            $filters = "*.(".MediaUtil::supportedAudioExtensions().")";
	            $accept = 'audio/*';
	            break;
	        case "video":
	            $filters = "*.(".MediaUtil::supportedVideoExtensions().")";
	            $accept = 'video/*';
	            break;
	        case "document":
	            $filters = "*.(".MediaUtil::supportedDocumentExtensions().")";
	            $accept = 'document/*';
	            break;
	        default:
	            break;
	    }
	    
	    return $this->render('MediaBundle:Admin:embed_file.html.twig', [
	        'filters' => $filters,
	        'type' => $type,
	        'accept' => $accept,
	        'data' => $data,
	        'formData' => $formData,
	        'multiple' => $multiple,
	        'context' => $context
	        
	    ]);
	}
	
	public function showFileAction(Request $request, $id) {
	    $em = $this->get('doctrine.orm.entity_manager');
	    
	    if (!$file = $em->find(File::class, $id)) {
	        $file = $em->getRepository(File::class)->findOneBy(['path' => $id]);
	    }
	    
	    if ($file->isPicture()) {
	        $type = File::PICTURE;
	        $metadata = $em->getRepository(Picture::class)->findOneBy(['file' => $id]);
	    }elseif ($file->isAudio()) {
	        $type = File::AUDIO;
	        $metadata = $em->getRepository(Audio::class)->findOneBy(['file' => $id]);
	    }elseif ($file->isVideo()) {
	        $type = File::VIDEO;
	        $metadata = $em->getRepository(Video::class)->findOneBy(['file' => $id]);
	    }elseif ($file->isDocument()) {
	        $type = File::DOCUMENT;
	        $metadata = $em->getRepository(Document::class)->findOneBy(['file' => $id]);
	    }else {
	        $type = null;
	        $metadata = null;
	    }
	    
	    return $this->render('MediaBundle:Admin:show_file.html.twig', ['file' => $file, 'type' => $type, 'metadata' => $metadata]);
	}
	
	/**
	 * Upload Media From Another App
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function uploadFileAction(Request $request){
	    $folder = $this->get('media.file_manager')->createFolder($request->query->get('context'), $this->getUser());
	    $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $this->getUser());
	    
	    if (count($media) == 1) {
	        $data = ['url' => $media[0]->getPath(), 'id' => $media[0]->getId()];
	        
	        if ($media[0]->isPicture()) {
	            $data['type'] = File::PICTURE;
	        }elseif ($media[0]->isAudio()) {
	            $data['type'] = File::AUDIO;
	        }elseif ($media[0]->isVideo()) {
	            $data['type'] = File::VIDEO;
	        }else {
	            $data['type'] = File::DOCUMENT;
	        }
	    }else {
	        $urls = $ids = $types = [];
	        
	        foreach ($media as $medium){
	            $urls[] = $medium->getPath();
	            $ids[] = $medium->getId();
	            
	            if ($media[0]->isPicture()) {
	                $types[] = File::PICTURE;
	            }elseif ($media[0]->isAudio()) {
	                $types[] = File::AUDIO;
	            }elseif ($media[0]->isVideo()) {
	                $types[] = File::VIDEO;
	            }else {
	                $types[] = File::DOCUMENT;
	            }
	        }
	        
	        $data = ['url' => implode(',', $urls), 'id' => implode(',', $ids), 'type' => implode(',', $types)];
	    }
	    
	    return new JsonResponse($data);
	}
	
	/**
	 * Add file
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addFileAction(Request $request) {
	    $em = $this->getDoctrine()->getManager();
	    $folderId = $request->query->get('folder');
	    $folder = $folderId ? $em->getRepository(Folder::class)->find($folderId) : null;
	    
        if (true === $request->isMethod('POST')) {
            $data = $request->request->all();
            
            if ($data['source'] === "local") { // local
//                 $file = $em->getRepository(File::class)->findOneBy(['path' => $data['path']]);
                $file = $em->getRepository(File::class)->find($data['file']);
            }else { // Remote
                if (isset($data['uploadable']) && $data['uploadable'] === "checked"){
                    $file = $this->get('media.upload_manager')->uploadFromUrl($data['path'], $this->getUser());
                }else {
                    $file = new File();
                    $file->setName($data['name']);
                    $file->setPath($data['path']);
                    
                    $em->persist($file);
                }
            }
            
            $file->setDisplayName($data['name']);
            $file->setCaption($data['caption']);
            
            $folder = false === empty($data['folder']) ? $em->getRepository(Folder::class)->find($data['folder']) : $this->get('media.file_manager')->createFolder(Folder::ROOT_APP_NAME, $this->getUser());
            $folder->addFile($file->getId());
            
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('media.file.create.success', ['%fileName%' => $file->getName()], 'media'));
            
            if (false === empty($data['folder'])) {
                return $this->redirect($this->generateUrl('puzzle_admin_media_folder_show', ['id' => $data['folder']]));
            }
            
            return $this->redirect($this->generateUrl('puzzle_admin_media_file_list'));
        }
        
        return $this->render('MediaBundle:Admin:add_file.html.twig', [
            'folder' => $folder
        ]);
    }
    
    /**
     * Add files
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addFilesAction(Request $request) {
        $folderId = $request->query->get('folder');
        $folder = $folderId ? $this->getDoctrine()->getRepository(Folder::class)->find($folderId) : null;
        
        return $this->render('MediaBundle:Admin:add_files.html.twig', [
            'folder' => $folder,
            'filters' => $folder && $folder->getAllowedExtensions() ? implode(',', $folder->getAllowedExtensions()) : '*',
            'refreshUrl' => $folderId ? $this->generateUrl('puzzle_admin_media_folder_show', ['id' => $folderId]) : null
        ]);
    }
    
    /**
     * Update file
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFileAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository(File::class)->find($id);
        $redirectUrl = false === empty($request->query->get('folder')) ? 
                        $this->generateUrl('puzzle_admin_media_folder_show', ['id' => $request->query->get('folder')]) :
                        $this->generateUrl('puzzle_admin_media_file_list');
        
        $form = $this->createForm(FileUpdateType::class, $file, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_media_file_update', ['id' => $id, 'folder' => $request->query->get('folder')])
        ]);
        
        $form->handleRequest($request);
        
        if (true === $form->isSubmitted()) {
            $data = $request->request->all();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('media.file.update.success', ['%fileName%' => $file->getName()], 'media'));
            return $this->redirect($data['redirect_url'] ??  $this->generateUrl('puzzle_admin_media_file_list'));
        }
        
        return $this->render('MediaBundle:Admin:update_file.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
            'redirectUrl' => $redirectUrl
        ]);
    }
    
    /**
     * Update file
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFileMetadataAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository(File::class)->find($id);
        $redirectUrl = false === empty($request->query->get('folder')) ?
                       $this->generateUrl('puzzle_admin_media_folder_show', ['id' => $request->query->get('folder')]) :
                       $this->generateUrl('puzzle_admin_media_file_list');
        
       $formType = null;
        if ($file->isAudio()) {
            $fileType = $em->getRepository(Audio::class)->findOneBy(['file' => $id]);
            $formType = AudioUpdateType::class;
            $type = File::AUDIO;
        }elseif ($file->isVideo()) {
            $fileType = $em->getRepository(Video::class)->findOneBy(['file' => $id]);
            $formType = VideoUpdateType::class;
            $type = File::VIDEO;
        }
        
        $form = $this->createForm($formType, $fileType, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_media_file_update_metadata', ['id' => $id, 'folder' => $request->query->get('folder')])
        ]);
        
        $form->handleRequest($request);
        
        if (true === $form->isSubmitted()) {
            $data = $request->request->all();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('media.file.update.success', ['%fileName%' => $file->getName()], 'media'));
            return $this->redirect($data['redirect_url'] ??  $this->generateUrl('puzzle_admin_media_file_list'));
        }
        
        return $this->render('MediaBundle:Admin:update_file_metadata.html.twig', [
            'file' => $fileType,
            'type' => $type,
            'form' => $form->createView(),
            'redirectUrl' => $redirectUrl
        ]);
    }
    

    /***
     * Delete File
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFileAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$file = $em->find(File::class, $id)) {
            $file = $em->getRepository(File::class)->findOneBy(['path' => $id]);
        }
        
        $this->get('event_dispatcher')->dispatch(MediaEvents::DELETING_FILE, new FileEvent([
            'absolutePath' => $file->getAbsolutePath()
        ]));
        
        $message = $this->get('translator')->trans('media.file.delete.success', ['%fileName%' => $file->getName()], 'media');
        
        if ($file->isPicture()) {
            $picture = $em->getRepository(Picture::class)->findOneBy(['file' => $id]);
            $em->remove($picture);
        }elseif ($file->isAudio()) {
            $audio = $em->getRepository(Audio::class)->findOneBy(['file' => $id]);
            $em->remove($audio);
        }elseif ($file->isVideo()) {
            $video = $em->getRepository(Video::class)->findOneBy(['file' => $id]);
            $em->remove($video);
        }elseif ($file->isDocument()) {
            $document = $em->getRepository(Document::class)->findOneBy(['file' => $id]);
            $em->remove($document);
        }
        
        $em->remove($file);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_media_file_list');
    }
    
    
    /***
     * List folders
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFoldersAction(Request $request) {
        $er = $this->getDoctrine()->getRepository(Folder::class);
        
        $folderDefault = $er->findOneBy(['appName' => Folder::ROOT_APP_NAME, 'name' => Folder::ROOT_NAME]);
        $folders = $er->findByReverseAppName('media');
        
        return $this->render("MediaBundle:Admin:list_folders.html.twig", array(
            'folders' => $folders,
            'folderDefault' => $folderDefault
        ));
    }
    
    /***
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browseFoldersAction(Request $request) {
        $er = $this->getDoctrine()->getRepository(Folder::class);
        
        if ($request->query->get('folder') === null || ! $parent = $er->find($request->query->get('folder'))) {
            $parent = $er->findOneBy(['appName' => Folder::ROOT_APP_NAME, 'name' => Folder::ROOT_NAME]);
        }
        
        $folders = $er->findByAppName(Folder::ROOT_APP_NAME, $parent->getId());
        
        return $this->render("MediaBundle:Admin:browse_folders.html.twig", array(
            'operation' => $request->query->get('operation'),
            'parent' => $parent,
            'folders' => $folders
        ));
    }
    
    
    /***
     * Show Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        $childs = $em->getRepository(Folder::class)->findByName($request->query->get('search'), $folder->getId());
        $files = false === empty($folder->getFiles()) ? $em->getRepository(File::class)->findByIds($folder->getFiles()) : null;
            
        return $this->render("MediaBundle:Admin:show_folder.html.twig", array(
            'folder' => $folder,
            'childs' => $childs,
            'files' => $files
        ));
    }
    
    
    /***
     * Create Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFolderAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $parentId = $request->query->get('parent');
        $parent = $em->getRepository(Folder::class)->find($parentId);
        
        $folder = new Folder();
        $folder->setParent($parent);
        
        $form = $this->createForm(FolderCreateType::class, $folder, [
            'method' => 'POST',
            'action' => $parent ?
                        $this->generateUrl('puzzle_admin_media_folder_create', ['parent' => $parentId]) :
                        $this->generateUrl('puzzle_admin_media_folder_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $folder->setAppName(Folder::ROOT_APP_NAME);
            $folder->setAllowedExtensions($folder->getAllowedExtensions() !== null ? explode('|', $folder->getAllowedExtensions()) : null);
            
            if ($folder->getParent() === null) {
                $parent = new Folder();
                $parent->setName(Folder::ROOT_NAME);
                $parent->setAppName(Folder::ROOT_APP_NAME);
                
                $em->persist($parent);
            }
            
            $em->persist($folder);
            $em->flush();
            
            $this->get('event_dispatcher')->dispatch(MediaEvents::CREATE_FOLDER, new FolderEvent($folder));
            $message = $this->get('translator')->trans('media.folder.create.success', ['%folderName%' => $folder->getName()], 'media');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            
            if ($folder->getParent() !== null) {
                return $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getParent()->getId()));
            }
            
            return $this->redirectToRoute('puzzle_admin_media_folder_list');
        }
        
        return $this->render("MediaBundle:Admin:create_folder.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        $folder->setAllowedExtensions(implode('|', $folder->getAllowedExtensions()));
        
        $oldAbsolutePath = $folder->getAbsolutePath();
        $form = $this->createForm(FolderUpdateType::class, $folder, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_media_folder_update', ['id' => $folder->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $folder->setAllowedExtensions($folder->getAllowedExtensions() !== null ? explode(',', $folder->getAllowedExtensions()) : null);
            $em->flush();
            
            if ($oldAbsolutePath != $folder->getAbsolutePath()) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::RENAME_FOLDER, new FolderEvent($folder, ['oldAbsolutePath' => $oldAbsolutePath]));
            }
            
            $message = $this->get('translator')->trans('media.folder.update.success', ['%folderName%' => $folder->getName()], 'media');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getId()));
        }
        
        return $this->render("MediaBundle:Admin:update_folder.html.twig", [
            'folder' => $folder,
            'form' => $form->createView()
        ]);
    }
    
    /**
     *
     * Update Folder by adding file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderByAddingFilesAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        if (true === $request->isMethod('POST')) {
            $data = $request->request->all();
            
            if (isset($data['files_to_add'])) {
                $filesTaAdd = is_string($data['files_to_add']) ? explode(',', $data['files_to_add']) : $data['files_to_add'];
                
                foreach ($filesTaAdd as $fileTaAdd) {
                    $folder->addFile($fileTaAdd);
                }
            }
            
            $em->flush();
            
            if (isset($data['operation']) && $data['operation'] == "move") {
                $folderEvent = new FolderEvent($folder, ['preserve_files' => false]);
            }else {
                $folderEvent = new FolderEvent($folder);
            }
            
            $this->get('event_dispatcher')->dispatch(MediaEvents::ADD_FILES_TO_FOLDER, $folderEvent);
            
            $message = $this->get('translator')->trans('media.folder.update.success', ['%folderName%' => $folder->getName()], 'media');
            
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getId()));
        }
        
        return $this->render('MediaBundle:Admin:update_folder_add_files.html.twig', ['folder' => $folder]);
    }
    
    /**
     *
     * Update Folder by removing files
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderByRemovingFilesAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if (isset($data['ids']) === true) {
            $list = explode(',', $data['ids']);
            foreach ($list as $item){
                $file = $em->getRepository(File::class)->find($item);
                $folder->removeFile($file->getId());
                $this->get('event_dispatcher')->dispatch(MediaEvents::REMOVE_FILE, new FileEvent([
                    'absolutePath' => $file->getAbsolutePath()
                ]));
                
                if ($file->isPicture()) {
                    $picture = $em->getRepository(Picture::class)->findOneBy(['file' => $file->getId()]);
                    $em->remove($picture);
                }elseif ($file->isAudio()) {
                    $audio = $em->getRepository(Audio::class)->findOneBy(['file' => $file->getId()]);
                    $em->remove($audio);
                }elseif ($file->isVideo()) {
                    $video = $em->getRepository(Video::class)->findOneBy(['file' => $file->getId()]);
                    $em->remove($video);
                }elseif ($file->isDocument()) {
                    $document = $em->getRepository(Document::class)->findOneBy(['file' => $file->getId()]);
                    $em->remove($document);
                }
                
                $em->remove($file);
            }
        }
        
        $em->flush();
        $message = $this->get('translator')->trans('media.folder.update.success', ['%folderName%' => $folder->getName()], 'media');
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getId()));
    }
    
    /**
     *
     * Update Folder by removing file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFolderByRemovingFileAction(Request $request, $id, $fileId) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        if (!$file = $em->find(File::class, $fileId)) {
            $file = $em->getRepository(File::class)->findOneBy(['path' => $id]);
        }
        
        $folder->removeFile($file->getId());
        
        $message = $this->get('translator')->trans('media.folder.update.success', ['%folderName%' => $folder->getName()], 'media');
        
        $this->get('event_dispatcher')->dispatch(MediaEvents::DELETING_FILE, new FileEvent([
            'absolutePath' => $file->getAbsolutePath()
        ]));
        
        if ($file->isPicture()) {
            $picture = $em->getRepository(Picture::class)->findOneBy(['file' => $file->getId()]);
            $em->remove($picture);
        }elseif ($file->isAudio()) {
            $audio = $em->getRepository(Audio::class)->findOneBy(['file' => $file->getId()]);
            $em->remove($audio);
        }elseif ($file->isVideo()) {
            $video = $em->getRepository(Video::class)->findOneBy(['file' => $file->getId()]);
            $em->remove($video);
        }elseif ($file->isDocument()) {
            $document = $em->getRepository(Document::class)->findOneBy(['file' => $file->getId()]);
            $em->remove($document);
        }
        
        $em->remove($file);
        $em->flush();
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getId()));
    }
    
    
    /***
     * Delete Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        if ($folder->getParent()) {
            $route = $this->redirectToRoute('puzzle_admin_media_folder_show', array('id' => $folder->getParent()->getId()));
        }else {
            $route = $this->redirectToRoute('puzzle_admin_media_folder_list');
        }
        
        $message = $this->get('translator')->trans('media.folder.update.success', ['%folderName%' => $folder->getName()], 'media');
        $this->get('event_dispatcher')->dispatch(MediaEvents::DELETING_FOLDER, new FolderEvent($folder));
        
        $em->remove($folder);
        $em->flush();
        
        $this->addFlash('success', $message);
        return $route;
    }
    
    
    /**
     * Compress folder
     * 
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function compressFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (!$folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        if (false === $this->get('media.file_manager')->zipDir($folder->getAbsolutePath())) {
            $message = $this->get('translator')->trans('media.folder.compress.error', ['%folderName%' => $folder->getName()], 'media');
            
            if (true === $request->isXmlHttpRequest()) {
                return new JsonResponse($message, 500);
            }
            
            $this->addFlash('error', $message);
            return $this->redirectToRoute('puzzle_admin_media_folder_show', ['id' => $id]);
        }
        
        $targetUrl = $folder->getPath().'.zip';
        $message = $this->get('translator')->trans('media.folder.compress.success', ['%folderName%' => $folder->getName()], 'media');
        
        if (true === $request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => $message, 'target' => $targetUrl], 200);
        }
        
        return $this->redirect($folder->getPath().'.zip');
    }
    
    /***
     * Show Comments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request)
    {
        $comments = $this->getDoctrine()
                        ->getRepository(Comment::class)
                        ->findBy(['file' => $request->get('file')],['createdAt' => 'DESC']);
        
        return $this->render("MediaBundle:Admin:list_comments.html.twig", array(
            'comments' => $comments
        ));
    }
    
    /**
     * Update Comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCommentAction(Request $request, $id)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        
        if(isset($data['is_visible']) && $data['is_visible'] == "on"){
            $comment->setIsVisbile(true);
        }else{
            $comment->setIsVisbile(false);
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirect($this->generateUrl('puzzle_admin_media_comment_list'));
    }
    
    
    /***
     * Delete comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirect($this->generateUrl('puzzle_admin_media_comment_list'));
    }
}
