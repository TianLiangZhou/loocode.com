import {AfterViewInit, ChangeDetectorRef, Component, ElementRef, Inject, Input, OnInit, ViewChild} from '@angular/core';
import {
  NB_DATE_ADAPTER,
  NbAccordionComponent,
  NbDatepickerAdapter,
  NbDateTimePickerComponent,
  NbSidebarService, NbTagComponent,
  NbTagInputDirective
} from "@nebular/theme";
import * as ClassicEditor from "../../../../ckeditor5/build/ckeditor";
import {environment} from "../../../../environments/environment";
import {FormControl} from "@angular/forms";
import {DynamicScriptLoaderService} from "../../../@core/services/dynamic.script.loader.service";
import {ActivatedRoute} from "@angular/router";
import {debounceTime, distinctUntilChanged, filter, startWith, switchMap} from "rxjs/operators";
import {CATEGORIES, POST_SHOW, POST_STORE, POST_UPDATE, TAGS} from "../../../@core/app.interface.data";
import {AppResponseDataOptions} from "../../../@core/app.data.options";
import {HttpClient} from "@angular/common/http";
import {ConfigurationService} from "../../../@core/services/configuration.service";
import {ToastService} from "../../../@core/services/toast.service";
import {CKFinderService} from "../../../@core/services/ckfinder.service";

@Component({
  selector: 'app-post',
  templateUrl: './post.component.html',
  styleUrls: ['./post.component.scss']
})
export class PostComponent implements OnInit, AfterViewInit {
  @Input() meta: boolean = true;
  @Input() store: string;
  @Input() update: string;
  @Input() show: string;
  private accordion: NbAccordionComponent;
  editorMode = "markdown";
  submitted: boolean = false;
  @ViewChild('accordionComponent', {static: false}) set content(content: NbAccordionComponent) {
    if(content) { // initially setter gets called with undefined
      this.accordion = content;
      this.accordion.openAll();
      this.cd.detectChanges();
    }
  }
  @ViewChild('eyeButton', { read: ElementRef }) eyeButton:ElementRef;
  id: number = 0;
  post: any = {
    post_title: "",
    post_excerpt: "",
    post_content: "",
    post_status: "draft", // "draft",
    post_type: "post",
    comment_status: "open",
    ping_status: "open",
    categories: [],
    tags: [],
    password: "",
    post_date: "",
    meta: {
      keyword: "",
      description: "",
      featured_media: "",
    }
  };
  eye: string = "open";
  setting: boolean = true;
  Editor = ClassicEditor;
  editor;
  editorOptions: any = {
    image: {
      resizeOptions: [
        {
          name: 'resizeImage:original',
          label: 'Original',
          value: null
        },
        {
          name: 'resizeImage:20',
          label: '20%',
          value: '20'
        },
        {
          name: 'resizeImage:50',
          label: '50%',
          value: '50'
        },
        {
          name: 'resizeImage:75',
          label: '75%',
          value: '75'
        }
      ],
      toolbar: [
        'imageStyle:full',
        'imageStyle:alignLeft',
        'imageStyle:alignRight',
        '|',
        'resizeImage',
        '|',
        'imageTextAlternative'
      ]
    },
    language: 'zh-cn',
    toolbar: [
      'ckfinder', 'code', 'codeBlock', 'undo', 'redo', 'alignment:left', 'alignment:right', 'alignment:center', 'alignment:justify',
      'alignment', 'fontSize', 'fontFamily', 'removeHighlight', 'highlight', 'bold', 'italic',
      'blockQuote', 'imageTextAlternative', 'heading', 'indent', 'outdent', 'link',
      'numberedList', 'bulletedList', 'mediaEmbed', 'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells'
    ],
    placeholder: "在这里撰写你的内容",
    ckfinder: {
      uploadUrl: environment.gateway + '/ckfinder/connector?command=QuickUpload&type=Files&responseType=json',
    }
  };
  tags: Map<string, string> = new Map<string, string>();
  filteredTags$: any[] = [];
  inputFormControl: FormControl = new FormControl();
  categories: any[] = [];
  @ViewChild(NbTagInputDirective, { read: ElementRef }) tagInput: ElementRef<HTMLInputElement>;

  constructor(
    private http: HttpClient,
    private loadScript: DynamicScriptLoaderService,
    private cd: ChangeDetectorRef,
    private sidebarService: NbSidebarService,
    private activateRoute: ActivatedRoute,
    @Inject(NB_DATE_ADAPTER) protected datepickerAdapters: NbDatepickerAdapter<any>[],
    private appConfig: ConfigurationService,
    private toastService: ToastService,
    public ckfinder: CKFinderService,
  ) {

  }

  ngOnInit() {
    this.editorMode = this.appConfig.appConfig.editor || 'markdown';
    this.inputFormControl.valueChanges.pipe(
      startWith(''),
      filter(value => value != ""),
      debounceTime(400),
      distinctUntilChanged(),
      switchMap(value => {
        if (value) {
          return this.http.get(TAGS + '?name=' + value);
        }
      })
    ).subscribe((res:AppResponseDataOptions) => {
      if (res.data.data.length > 0) {
        this.filteredTags$ = res.data.data;
      }
    });
    this.ckfinder.subscribe((res) => {
      this.post.meta.featured_media = res[0].url;
    });
    if (this.meta) {
      this.http.get(CATEGORIES).subscribe((res: AppResponseDataOptions) => {
        if (res.code == 200) {
          res.data.forEach((item) => {
            item.checked = this.post.categories.includes(item.term_taxonomy_id)
          });
          this.categories = res.data
        }
      });
    }
    this.activateRoute.paramMap.subscribe(paramMap => {
      if (paramMap.get('id')) {
        this.http.get(this.show.replace("{id}", paramMap.get('id'))).subscribe((res: AppResponseDataOptions) => {
          if (res.code !== 200) {
            return;
          }
          this.id = +paramMap.get('id');
          this.post = res.data;
          if (this.meta) {
            res.data.terms.forEach((item) => {
              this.tags.set(item.name, item.id);
            });
            const categories = this.categories;
            categories.forEach((item) => {
              item.checked = this.post.categories.includes(item.term_taxonomy_id)
            });
            this.categories = categories;
          }
        });
      }
    });
  }

  @ViewChild("dateTimePicker") datepicker: NbDateTimePickerComponent<any>;
  @ViewChild("inputBtnElement", {read: ElementRef}) inputBtnElement;
  ngAfterViewInit(): void {
    if (this.datepicker) {
      this.datepicker.attach(this.inputBtnElement)
      this.datepicker.valueChange.subscribe((value) => {
        const datepickerAdapter = this.datepickerAdapters.find(({picker}) => this.datepicker instanceof picker);
        value = datepickerAdapter.format(value, null);
        this.inputBtnElement.nativeElement.innerHTML = value;
        this.datepicker.hide();
        this.post.post_date = value;
      });
    }
    setTimeout(() => {
      // this.sidebarService.toggle(false, 'menu-sidebar');
      let toggle = document.querySelector<HTMLElement>('.sidebar-toggle');
      if (toggle) {
        toggle.click();
      }
    }, 500);
  }



  onTagRemove(tagToRemove: NbTagComponent): void {
    this.tags.delete(tagToRemove.text);
  }

  onTagAdd(value: string): void {
    if (value) {
      let isFilteredTag = false;
      this.filteredTags$.forEach((item) => {
        if (item.term_taxonomy_id == value) {
          this.tags.set(item.term.name, item.term_taxonomy_id);
          isFilteredTag = true;
        }
      })
      if (isFilteredTag) {
        this.filteredTags$ = this.filteredTags$.filter(o => o.term.name !== value);
      } else {
        this.tags.set(value, value);
      }
    }
    this.tagInput.nativeElement.value = '';
  }

  onReady(editor) {
    this.editor = editor;
    editor.ui.getEditableElement().parentElement.insertBefore(
      editor.ui.view.toolbar.element,
      editor.ui.getEditableElement()
    );
  }

  action(status: string) {
    if (status == 'publish') {
      if (!this.post.post_title) {
        return this.toastService.showToast("danger", "写文章","标题不能为空");
      }
    }
    if (this.tags.size > 0) {
      const iterator = this.tags.values()
      let v = iterator.next()
      while (v.value) {
        if (!this.post.tags.includes(v.value)) {
          this.post.tags.push(v.value)
        }
        v = iterator.next()
      }
    }
    if (this.categories.length > 0) {
      this.categories.forEach((item) => {
        if (item.checked && !this.post.categories.includes(item.term_taxonomy_id)) {
          this.post.categories.push(item.term_taxonomy_id);
        }
      })
    }
    if (this.eye == "private") {
      status = "private";
    }
    if (this.post.post_date.length > 0) {
      const timestamp = new Date(this.post.post_date).getTime() / 1000 | 0;
      if (timestamp > this.appConfig.appConfig.timestamp) {
        status = "future";
      }
    }
    this.post.post_status = status;
    let url = this.store;
    if (this.id > 0) {
      url = this.update.replace('{id}', this.id.toString());
    }
    this.http.post(url, this.post).subscribe((res: AppResponseDataOptions) => {
      this.toastService.showResponseToast(res.code, "", res.message);
      this.submitted = false;
      if (res.code === 200 && res.data.id) {
        this.id = res.data.id;
      }
    });
  }

  preview() {
    if (this.id > 0) {
      window.open(location.protocol + "//" + location.host + "/post/" + this.id + '?preview=true')
    } else {
      this.toastService.showToast('danger', "预览", "只有保存之后才可以预览");
    }
  }

  commentStatus(status: boolean) {
    this.post.comment_status = status ? "open": "closed";
  }

  pingStatus(status: boolean) {
    this.post.ping_status = status ? "open": "closed";
  }

  onEye($event) {
    let name = "公开";
    switch ($event) {
      case "private":
        name = "私密"
        break;
      case "password":
        if (this.post.password.length > 0) {
          name = "密码保护";
        }
        break;
    }
    if ($event !== "password") {
      this.post.password = "";
    }
    this.eyeButton.nativeElement.innerHTML = name;
  }
  datetime($event: MouseEvent) {
    this.datepicker.show();
  }

  popoverChange($event: { isShown: boolean }) {
    if ($event.isShown == false && this.post.password < 1 && this.eye == "password") {
      this.eye = "open";
    }
  }

  onSetting() {
    this.setting = !this.setting;
  }
}
