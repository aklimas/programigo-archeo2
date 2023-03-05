#Pliki
##Wgrywanie
    /**
     * Wgrywanie plików na serwer wraz z możliwością ustalania wielkości.
     *
     * @param $file 'plik przesłany z formularza'
     * @param string $path           'ścieżka zapisu: domyślnie "uploads/"'
     * @param int[]  $sizes          'maksymalna szerość obrazka domyślnie 800, może być kilka'
     * @param bool   $deleteOriginal 'czy usuwać oryginalny rozmiar zdjęcia - domyślnie / tak'
     * @param bool   $uniqueName     'czy nazwa ma mieć unikalny ciąg znaków w nazie - domyślnie / tak'
     */
    public function uploadFile($file, string $path = 'uploads/', array $sizes = [800], bool $deleteOriginal = true, bool $uniqueName = true)
####
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
    public function uploadImageFromBase64($image, $name, string $ext = 'jpg', bool $path = false)
##Usuwanie
    /**
     * Funkcja usuwająca pliki  z bazy i pliku.
     *
     * @param false $deleteAll 'czy usuwać wszystkie wersje pliku - wsystkie rozmiary, domyślnie false;
     */
    public function deleteFileById($id, bool $deleteAll = false)
#####
    /**
     * Funkcja usuwająca z dysku o danej nazwie.
     * @param $fileName 'pelna nazwa pliku'
     * @param null $path 'opcjonalnie: ścieżka, domyślnie uploads/'
     * @return bool
     */
    public function deleteFile($fileName, $path = null)
