import {
  ChangeDetectorRef,
  Component,
  Directive,
  EventEmitter,
  Inject,
  Injectable,
  Injector,
  OnInit,
  Output,
  TemplateRef
} from '@angular/core';
import {ActivatedRoute, NavigationEnd, PRIMARY_OUTLET, Router, RoutesRecognized} from '@angular/router';
import {HttpClient} from '@angular/common/http';
import {
  NB_DATE_ADAPTER,
  NbComponentType,
  NbDatepickerAdapter,
  NbDialogService,
  NbSidebarService,
  NbWindowRef,
  NbWindowService,
  NbWindowState
} from '@nebular/theme';
import {ToastService} from './services/toast.service';
import {UploadService} from './services/upload.service';
import {DomSanitizer, Title} from '@angular/platform-browser';
import {environment} from '../../environments/environment';
import {FormBuilder} from '@angular/forms';
import {WindowContent, AppConfiguration} from './app.data.options';
import {ServerDataSource} from './services/server.data.source';
import {ReplaySubject} from 'rxjs';
import {DynamicScriptLoaderService} from './services/dynamic.script.loader.service';
import {ConfigurationService} from './services/configuration.service';
import {AppInjector} from "./app.injector";
import {filter, flatMap, map} from "rxjs/operators";

@Component({
  template: ``,
  selector: 'app-base'
})
export abstract class BaseComponent implements OnInit {

  loading = false;

  title: string;

  mode = {create: '添加', editor: '更新', delete: '删除', preview: '预览'};

  currentMode = 'create';

  source: ServerDataSource;

  protected nbWindowRef: NbWindowRef;

  protected storeWindow: TemplateRef<any> | NbComponentType<any>;

  protected serviceSourceConf = new ReplaySubject<object>(1);

  submitted: boolean;

  finderFileChoose = new EventEmitter<any>();
  appConfig: AppConfiguration;

  protected http: HttpClient;
  protected windowService: NbWindowService;
  protected toastService: ToastService;
  protected dialogService: NbDialogService;
  protected router: Router;
  protected injector: Injector;

  constructor(
    private readonly route: ActivatedRoute
  ) {
    this.injector = AppInjector.getInjector();
    this.http = this.injector.get<HttpClient>(HttpClient);
    this.windowService = this.injector.get<NbWindowService>(NbWindowService);
    this.toastService = this.injector.get<ToastService>(ToastService);
    this.dialogService = this.injector.get<NbDialogService>(NbDialogService);
    this.appConfig = this.injector.get<ConfigurationService>(ConfigurationService).appConfig;
    this.router = this.injector.get<Router>(Router);
  }

  ngOnInit(): void {
    this.serviceSourceConf.subscribe((serviceSourceConf) => {
      this.source = new ServerDataSource(this.http, serviceSourceConf);
    });
    this.init();
    this.route.data.subscribe(data => {
      this.title = data.title;
    })
  }

  /**
   *
   * @param message 消息内容
   */
  successToast(message: string) {
    this.toastService.showToast(
      'success',
      this.operationSubject(),
      message
    );
    return true;
  }

  /**
   *
   * @param message 消息内容
   */
  failureToast(message: string) {
    this.toastService.showToast(
      'danger',
      this.operationSubject(),
      message
    );
    return true;
  }

  /**
   * 当前操作主题
   */
  operationSubject(): string {
    return this.withOperationSubject(this.currentMode);
  }

  /**
   *
   * 当前操作名称
   */
  operationName(): string {
    return this.mode[this.currentMode];
  }

  /**
   *
   * @param mode 模式
   */
  withOperationName(mode: string): string {
    return this.mode[mode];
  }

  /**
   *
   * @param mode 械式
   */
  withOperationSubject(mode): string {
    return this.mode[mode] + this.title;
  }

  /**
   *
   * @param mode 操作模式 编辑或创建
   * @param windowClass window class name
   * @param data 传递的参数
   */
  popupOperationDialog(mode: string, windowClass: string = 'col-lg-6', data: object = {}) {
    this.currentMode = mode;
    const content = data || {};
    (<WindowContent>content).mode = mode;
    this.nbWindowRef = this.windowService.open(this.storeWindow, {
      title: this.operationSubject(),
      windowClass: windowClass || 'col-lg-6',
      context: content,
      initialState: NbWindowState.FULL_SCREEN
    });
    this.nbWindowRef.onClose.subscribe(() => {
      this.onCloseDialogCallback();
    });
  }

  init() {
  }
  onCloseDialogCallback() {
  }
}
