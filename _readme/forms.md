#Formularze łączone
    $families = new Families();
    $address = new Addresses();

    $formData['families'] = $families;
    $formData['addresses'] = $address;

    $mergedForm = $this->createFormBuilder($formData)
        ->add('families', FamiliesType::class)
        ->add('addresses', AddressesType::class)
        ->add('submit', SubmitType::class, ['label' => 'Zapisz', 'attr' => ['class' => 'btn btn-primary btn-sm']])
        ->getForm();
    $mergedForm->handleRequest($request);

    if ($mergedForm->isSubmitted() && $mergedForm->isValid()) {
    }

#Wybór
    ->add('kondOfDome', ChoiceType::class, [
        'label' => 'Rodzaj kopuły',
        'choices'  => [
            '--wybierz--' => null,
            'Mała' => 'small',
            'Duża' => 'big',
        ],
    ])

#Przycisk wysyłania
    ->add('submit', SubmitType::class, ['label' => 'Zapisz', 'attr' => ['class' => 'btn btn-primary btn-sm mt-4']])
#Pole tekstowe
```
->add('name', TextType::class, [
    'required' => true, 
    'label' => 'Imię', 
    'attr' => [
        'class' => 'form-control-sm'
    ]
])
```
#Data
    ->add('startDate',DateType::class, [
        'widget' => 'single_text',
        'required' => false,
        'label' => 'Data rozpoczęcia',
        'attr' => [
            'class' => '',
        ], ])
#Wgrywanie plików
    ->add('file', FileType::class, [
        'label_attr' => ['class' => 'form-label'],
        'label' => 'Załącz zdjęcie',
        'attr' => ['class' => 'file', 'id' => 'upload_file'],
        'mapped' => false,
        'multiple' => true, //false jako pojedyńcze
        'required' => false,
        'constraints' => [
            /*new File([
                'maxSize' => '100000k',
                'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                    'text/x-comma-separated-values',
                ],
                'mimeTypesMessage' => 'Nieprawidłowy typ pliku',
            ]),*/
        ],
    ])
#Pobieranie danych z bazy danych
### Waluta

    ->add('currencies', EntityType::class, [
        'class' => Currencies::class,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('m')
                ->orderBy('m.title', 'ASC');
        },
        'choice_label' => function (Currencies $item) {
            return $item->getTitle();
        },
        'label' => 'Waluta',
        'required' => false,
        'empty_data' => null,
        'placeholder' => ' -- wybierz walutę --',
        'attr' => ['autocomplete' => 'off', 'class' => 'form-select form-select-sm'],
    ])

### Rodzaj stawki
    ->add('typesOfRates', EntityType::class, [
        'class' => TypesOfRates::class,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('m')
                ->orderBy('m.title', 'ASC');
        },
        'choice_label' => function (TypesOfRates $item) {
            return $item->getTitle();
        },
        'label' => 'Rodzaj stawki',
        'required' => false,
        'empty_data' => null,
        'placeholder' => ' -- wybierz rodzaj stawki --',
        'attr' => ['autocomplete' => 'off', 'class' => 'form-select form-select-sm'],
    ])

### Kraj
    ->add('country', EntityType::class, [
        'class' => Country::class,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('m')
                ->orderBy('m.title', 'ASC');
        },
        'choice_label' => function (Country $item) {
            return $item->getTitle();
        },
        'label' => 'Kraj',
        'required' => false,
        'empty_data' => null,
        'placeholder' => ' -- wybierz kraj --',
        'attr' => ['autocomplete' => 'off', 'class' => 'form-select form-select-sm'],
    ])