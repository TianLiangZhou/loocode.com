import {
  ChangeDetectionStrategy,
  ChangeDetectorRef,
  Component,
  forwardRef,
  HostBinding,
  Input,
  OnInit,
} from '@angular/core';
import {ControlValueAccessor, FormControl, NG_VALUE_ACCESSOR} from "@angular/forms";
import * as hljs from "highlight.js";

@Component({
  selector: 'app-markdown-editor',
  templateUrl: './markdown-editor.component.html',
  styleUrls: ['./markdown-editor.component.scss'],
  providers: [{
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => MarkdownEditorComponent),
    multi: true,
  }],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class MarkdownEditorComponent implements OnInit, ControlValueAccessor {

  //to allow multiple textarea on the same screen, need to set an uniqueId for the textarea
  @Input() control: FormControl;
  @HostBinding('class.focus') isFocus: boolean;

  onChange: any = () => { };
  onTouched: any = () => { };

  private _value: string = "";
  @Input()
  get value(): string {
    return this._value;
  }
  set value(value: string) {
    if (value !== this._value) {
      this._value = value;
      this.onChange(value);
    }
  }

  markdown: string
  preview: boolean;

  constructor(
    private changeDetector: ChangeDetectorRef,
  ) { }

  ngOnInit(): void {
    this.control = this.control ?? new FormControl();
    this.control.valueChanges.subscribe((value) => {
      this.markdown = value
    });
  }

  focus() {
    this.isFocus = true;
  }

  blur() {
    this.isFocus = false;
  }
  onPreview() {
    this.preview = !this.preview;
  }

  renderFinish() {
    document.querySelectorAll('pre code').forEach((block: HTMLElement) => {
      hljs.highlightBlock(block);
    });
  }

  registerOnChange(fn: any): void {
    this.onChange = fn;
  }

  registerOnTouched(fn: any): void {
    this.onTouched = fn;
  }

  writeValue(obj: any): void {
    this._value = obj;
    this.control.setValue(obj);
    this.changeDetector.markForCheck()
  }
}
