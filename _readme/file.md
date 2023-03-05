#Obsługa pliku
    // TODO Dodawanie plików
    if ($formFiles->has('file')) {
        $_file = $formFiles->get('file')->getData();
        if (null !== $_file and count($_file) > 1) {
            foreach ($_file as $f) {
                $result = $this->files->upload($f);
                $file = $this->files->getFile($result);
                $tickets->addFile($file);
            }
        } elseif (null !== $_file and 1 === count($_file)) {
            foreach ($_file as $f) {
                $result = $this->files->upload($f);
                $file = $this->files->getFile($result);
                $tickets->addFile($file);
            }
        }
    }
#Formularz
    $builder->add('file', FileType::class, [
    'label_attr' => ['class' => 'form-label'],
    'label' => false,
    'attr' => ['class' => 'file', 'id' => 'upload_file'],
    'mapped' => false,
    'multiple' => true,
    'required' => false,
    'constraints' => [
    //                new File([
    //                    'maxSize' => '100000k',
    //                    'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
    //                        'text/x-comma-separated-values',
    //                        'text/comma-separated-values',
    //                        'text/x-csv',
    //                        'text/csv',
    //                        'text/plain',
    //                        'application/octet-stream',
    //                        'application/vnd.ms-excel',
    //                        'application/x-csv',
    //                        'application/csv',
    //                        'application/excel',
    //                        'application/vnd.msexcel',
    //                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //                    ],
    //                    'mimeTypesMessage' => 'Nieprawidłowy typ pliku',
    //                ]),
    ],
    ]);

#TWIG
    {% block stylesheets %}{{ parent() }}
        {{ block("stylesheets", "_modules/_fileinput.html.twig") }}
        {{ block("stylesheets", "_modules/_glightbox.html.twig") }}
    {% endblock %}
    {% block javascripts_bottom %}{{ parent() }}
        {{ block("javascripts_bottom", "_modules/_fileinput.html.twig") }}
        {{ block("javascripts_bottom", "_modules/_glightbox.html.twig") }}
    {% endblock %}
#Wyświetlanie plików wraz z formularzem
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Pliki <span>| Dodatkowe</span></h5>
            <div class="row">
                {% for files in tickets.file %}
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body p-1 text-center">
                                {% set src = files.path ~ files.name %}
                                {% if files.extension == 'jpg' or files.extension == 'png' %}
                                    <a href="{{ src }}" class="lightbox">
                                        <img src="{{ src }}" alt="{{ files.alt }}"
                                             class="card-img-top">
                                    </a>
                                {% else %}
                                    <a href="{{ src }}" class="lightbox ">
                                        <i class="bi bi-camera-video-fill fs-4 mx-auto"></i>
                                    </a>
                                {% endif %}
                            </div>
                            <div class="card-footer p-1">
                                <a href="{{ path('tickets_deleteFile', {'id' : files.id }) }}"
                                   class="badge btn btn-danger ">Usuń</a>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
            {{ form_start(formFiles) }}
            {{ form_widget(formFiles) }}
            {{ form_end(formFiles) }}
        </div>
    </div>
