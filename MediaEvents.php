<?php

namespace Puzzle\MediaBundle;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
final class MediaEvents
{
    const CREATE_FILE = "media.create_file";
    const COPY_FILE = "media.copy_file";
    const RENAME_FILE = "media.rename_file";
    const DELETING_FILE = "media.deleting_file";
    
    const CREATE_FOLDER = "media.create_folder";
    const RENAME_FOLDER = "media.rename_folder";
    const DELETING_FOLDER = "media.deleting_folder";
    const ADD_FILES_TO_FOLDER = "media.add_files_to_folder";
    const REMOVE_FILES_TO_FOLDER = "media.remove_files_to_folder";
}