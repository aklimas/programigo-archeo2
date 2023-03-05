<?php
/**
 * Serwis do wgrywania plików.
 */

namespace App\Service;

use App\Entity\Core\Files\Files;
use App\Repository\Core\Files\FilesRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilesService
{
    public function __construct(
        private SluggerInterface $slugger,
        private KernelInterface $appKernel,
        private FilesRepository $files,
        private TextService $text,
        private $targetDirectory = 'uploads/')
    {
    }

    /**
     * Wgrywanie plików na serwer wraz z możliwością ustalania wielkości.
     *
     * @param $file 'plik przesłany z formularza'
     * @param string $path           'ścieżka zapisu: domyślnie "uploads/"'
     * @param int[]  $sizes          'maksymalna szerość obrazka domyślnie 800, może być kilka'
     * @param bool   $deleteOriginal 'czy usuwać oryginalny rozmiar zdjęcia - domyślnie / tak'
     * @param bool   $uniqueName     'czy nazwa ma mieć unikalny ciąg znaków w nazie - domyślnie / tak'
     */
    public function uploadFile($file, string $path = 'uploads/', array $sizes = [800], bool $deleteOriginal = true, bool $uniqueName = true): ?Files
    {
        $names = null;

        $this->path('/'.$path);
        $file = $this->files->findOneBy(['id' => $this->upload($file, $uniqueName)]);

        if (null !== $sizes) {
            $simpleImage = new SimpleImage();
            $names = $simpleImage->resizeImage($path, $file->getName(), $sizes);
        }

        if (true === $deleteOriginal) {
            $this->deleteFileById($file->getId());

            $dbFiles = new Files();
            $dbFiles->setName($names[0]);
            $dbFiles->setPath($this->targetDirectory);
            $dbFiles->setExtension($file->getExtension());
            $this->files->save($dbFiles, true);
            $file = $dbFiles;
        }

        return $file;
    }

    /**
     * Funkcja wgrywania obrazu z Base64.
     *
     * @param $image 'obraz w postaci kodu base64'
     * @param $name 'nazwa tworzonego pliku'
     * @param string $ext  'rozszerzenie pliku, domyślnie jpg'
     * @param bool   $path 'ścieżka zapisu pliku, domyślnie uploads/'
     *
     * @throws \Exception
     */
    public function uploadImageFromBase64($image, $name, string $ext = 'jpg', string $path = ''): Files
    {
        if ('' !== $path) {
            $this->path($path);
        }

        $projectRoot = $this->appKernel->getProjectDir();
        $newImage = new SimpleImage($image);

        $newImage->toFile($projectRoot.'/public/'.$this->targetDirectory.$name);

        $dbFiles = new Files();
        $dbFiles->setName($name);
        $dbFiles->setPath($this->targetDirectory);
        $dbFiles->setExtension($ext);
        $this->files->save($dbFiles, true);

        return $dbFiles;
    }

    /**
     * Funkcja usuwająca z dysku o danej nazwie.
     *
     * @param $fileName 'pelna nazwa pliku'
     * @param null $path 'opcjonalnie: ścieżka, domyślnie uploads/'
     */
    public function deleteFile($fileName, $path = null): bool
    {
        if (null === $path) {
            $this->targetDirectory = $path;
        }
        $filesystem = new Filesystem();
        $filesystem->remove($this->targetDirectory.'/'.$fileName);

        return true;
    }

    /**
     * Funkcja usuwająca pliki  z bazy i pliku.
     *
     * @param false $deleteAll 'czy usuwać wszystkie wersje pliku - wsystkie rozmiary, domyślnie false;
     */
    public function deleteFileById($id, bool $deleteAll = false): bool
    {
        $projectRoot = $this->appKernel->getProjectDir();
        $filesystem = new Filesystem();
        $_file = $this->files->findOneBy(['id' => $id]);

        if ($_file) {
            $filesystem->remove($projectRoot.'/public'.$_file->getPath().''.$_file->getName());
            $this->files->remove($_file, true);

            if (true === $deleteAll) {
                $finder = new Finder();
                $finder->files()->in($projectRoot.'/public'.$_file->getPath());

                $filename = $_file->getName();

                $pattern = "/\d\d\dx-/i";
                $m = preg_replace($pattern, '', $filename);
                // preg_match("/\[A-Za-z0-9]/", $filename, $m);

                $finder->files()->name('*'.$m);
                if ($finder->hasResults()) {
                    foreach ($finder as $file) {
                        $absoluteFilePath = $file->getRealPath();
                        $filesystem->remove($absoluteFilePath);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Funkcja zwraca Entity danego pliku po nazwie.
     *
     * @param $name 'nazwa wyszukiwanego pliku'
     */
    public function getFileByName($name): ?Files
    {
        return $this->files->findOneBy(['name' => $name]);
    }

    // ###########################################################

    public function path($path)
    {
        $this->targetDirectory = $path;
    }

    public function upload($uploadFile, $uniq = true): int|string|null
    {
        $projectRoot = $this->appKernel->getProjectDir();
        $completeTargetDirectory = $projectRoot.'/public/'.$this->targetDirectory;

        if ($uploadFile) {
            $originalFilename = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->text->shortNameWithoutSpace($originalFilename);
            $safeFilename = $this->slugger->slug($safeFilename);
            $extension = $uploadFile->guessExtension();
            if (true == $uniq) {
                $newFilename1 = $safeFilename.'-'.uniqid().'.'.$uploadFile->guessExtension();
            } else {
                $newFilename1 = $safeFilename.'.'.$uploadFile->guessExtension();
            }
            try {
                $uploadFile->move(
                    $completeTargetDirectory,
                    $newFilename1
                );

                $dbFiles = new Files();
                $dbFiles->setName($newFilename1);
                $dbFiles->setPath($this->targetDirectory);
                $dbFiles->setExtension($extension);
                $this->files->save($dbFiles, true);

                return $dbFiles->getId();
            } catch (FileException $e) {
                return $e->getMessage();
            }
        } else {
            return null;
        }
    }
}
